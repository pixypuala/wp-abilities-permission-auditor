<?php
/**
 * Serializes an audit result to a stable, deterministic JSON document.
 *
 * Unlike Report (which orders findings by severity for human reading), this
 * export is byte-for-byte reproducible for the same input: every map key is
 * sorted and no timestamp or environment data is embedded. That makes the
 * output safe to diff, snapshot, or check into version control as evidence.
 * The document carries an explicit schema version so consumers can evolve.
 *
 * @package Pixypuala\AbilitiesAuditor
 */

declare( strict_types=1 );

namespace Pixypuala\AbilitiesAuditor;

/**
 * Deterministic JSON serializer for a role/capability audit.
 */
final class AuditExport {

	/**
	 * Schema version of the emitted document. Bump on any breaking shape change.
	 *
	 * @var string
	 */
	public const SCHEMA_VERSION = '1.0.0';

	/**
	 * Build the deterministic export document.
	 *
	 * Role keys are sorted, each role's capabilities are sorted, and findings
	 * are ordered by role then capability so the output never depends on input
	 * ordering or the severity ranking used for display.
	 *
	 * @param array<string, string[]> $roles    Role slug => list of capability slugs.
	 * @param Finding[]               $findings Findings produced by the Auditor.
	 *
	 * @return array<string, mixed>
	 */
	public function to_array( array $roles, array $findings ): array {
		$sorted_roles = array();
		ksort( $roles, SORT_STRING );
		foreach ( $roles as $role => $capabilities ) {
			$caps = array_values( array_unique( $capabilities ) );
			sort( $caps, SORT_STRING );
			$sorted_roles[ $role ] = $caps;
		}

		$sorted_findings = $findings;
		usort(
			$sorted_findings,
			static function ( Finding $a, Finding $b ): int {
				return array( $a->role, $a->capability ) <=> array( $b->role, $b->capability );
			}
		);

		return array(
			'schemaVersion' => self::SCHEMA_VERSION,
			'roleCount'     => count( $sorted_roles ),
			'findingCount'  => count( $sorted_findings ),
			'roles'         => $sorted_roles,
			'findings'      => array_map( static fn ( Finding $f ): array => $f->to_array(), $sorted_findings ),
		);
	}

	/**
	 * Serialize the export document to a stable JSON string.
	 *
	 * @param array<string, string[]> $roles    Role slug => list of capability slugs.
	 * @param Finding[]               $findings Findings produced by the Auditor.
	 *
	 * @return string
	 */
	public function to_json( array $roles, array $findings ): string {
		return (string) json_encode(
			$this->to_array( $roles, $findings ),
			JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		) . "\n";
	}
}
