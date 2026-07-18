<?php
/**
 * Audits a role/capability map against the risk policy.
 *
 * Input is the shape WordPress exposes via get_editable_roles(): a map of role
 * slug to a list of granted capabilities. The auditor is framework-free — a WP
 * adapter (or the CLI reading a JSON export) supplies the map — so the risk
 * logic is fully unit-tested.
 *
 * @package Pixypuala\AbilitiesAuditor
 */

declare( strict_types=1 );

namespace Pixypuala\AbilitiesAuditor;

/**
 * Produces findings from a role/capability map.
 */
final class Auditor {

	public function __construct( private readonly Policy $policy = new Policy() ) {}

	/**
	 * Audit a role/capability map.
	 *
	 * @param array<string, string[]> $roles Role slug => list of capability slugs.
	 *
	 * @return Finding[] Findings, sorted most-severe first, then by role.
	 */
	public function audit( array $roles ): array {
		$dangerous  = $this->policy->dangerous_capabilities();
		$privileged = $this->policy->privileged_role();
		$findings   = array();

		foreach ( $roles as $role => $capabilities ) {
			if ( $role === $privileged ) {
				continue; // The administrator is expected to hold these.
			}
			foreach ( $capabilities as $capability ) {
				if ( isset( $dangerous[ $capability ] ) ) {
					$findings[] = new Finding(
						$role,
						$capability,
						$dangerous[ $capability ],
						sprintf(
							'Role "%s" holds the dangerous capability "%s"; normally only %s should.',
							$role,
							$capability,
							$privileged
						)
					);
				}
			}
		}

		// Most severe first so the CLI exit code and top-of-report reflect risk.
		usort(
			$findings,
			static function ( Finding $a, Finding $b ): int {
				return array( $b->severity->value, $a->role ) <=> array( $a->severity->value, $b->role );
			}
		);

		return $findings;
	}

	/**
	 * The highest severity among findings, or null when clean.
	 *
	 * @param Finding[] $findings Findings.
	 *
	 * @return Severity|null
	 */
	public function worst( array $findings ): ?Severity {
		$worst = null;
		foreach ( $findings as $finding ) {
			if ( null === $worst || $finding->severity->value > $worst->value ) {
				$worst = $finding->severity;
			}
		}
		return $worst;
	}
}
