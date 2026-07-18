<?php
/**
 * Maps the audit model to an Abilities-API-style structure.
 *
 * The WordPress Abilities API describes each ability as a named descriptor with
 * a label, a category, and a meta bag. This maps every audited role/capability
 * grant to one such descriptor, annotating whether the audit flagged it and at
 * what severity. That gives an abilities-shaped view of permission coverage —
 * flagged and clean alike — without depending on the Abilities API itself, so
 * the mapping is fully unit-tested. The output is deterministically ordered.
 *
 * @package Pixypuala\AbilitiesAuditor
 */

declare( strict_types=1 );

namespace Pixypuala\AbilitiesAuditor;

/**
 * Framework-free mapper from audit findings to ability descriptors.
 */
final class AbilitiesMap {

	/**
	 * Name prefix for generated ability identifiers.
	 *
	 * @var string
	 */
	public const NAME_PREFIX = 'abilities-auditor';

	/**
	 * Category assigned to every generated ability descriptor.
	 *
	 * @var string
	 */
	public const CATEGORY = 'permission-audit';

	/**
	 * Map audited roles and findings to a sorted list of ability descriptors.
	 *
	 * One descriptor is emitted per unique role/capability grant. Grants the
	 * auditor flagged carry `flagged => true` and the finding's severity; clean
	 * grants carry `flagged => false` and a null severity. Descriptors are sorted
	 * by name so the output never depends on input ordering.
	 *
	 * @param array<string, string[]> $roles    Role slug => granted capability slugs.
	 * @param Finding[]               $findings Findings produced by the Auditor.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function to_abilities( array $roles, array $findings ): array {
		$flagged = array();
		foreach ( $findings as $finding ) {
			$flagged[ $finding->role . '|' . $finding->capability ] = $finding->severity;
		}

		$abilities = array();
		foreach ( $roles as $role => $capabilities ) {
			foreach ( array_unique( $capabilities ) as $capability ) {
				$severity    = $flagged[ $role . '|' . $capability ] ?? null;
				$abilities[] = array(
					'name'     => sprintf( '%s/%s.%s', self::NAME_PREFIX, $role, $capability ),
					'label'    => sprintf( 'Role "%s" capability "%s"', $role, $capability ),
					'category' => self::CATEGORY,
					'meta'     => array(
						'role'       => (string) $role,
						'capability' => $capability,
						'flagged'    => null !== $severity,
						'severity'   => $severity?->label(),
					),
				);
			}
		}

		usort(
			$abilities,
			static function ( array $a, array $b ): int {
				return $a['name'] <=> $b['name'];
			}
		);

		return $abilities;
	}
}
