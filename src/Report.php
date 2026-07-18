<?php
/**
 * Renders audit findings as text or JSON.
 *
 * @package Pixypuala\AbilitiesAuditor
 */

declare( strict_types=1 );

namespace Pixypuala\AbilitiesAuditor;

/**
 * Formats findings for humans and machines.
 */
final class Report {

	/**
	 * @param Finding[] $findings Findings to render.
	 *
	 * @return string
	 */
	public function to_json( array $findings ): string {
		$data = array(
			'findingCount' => count( $findings ),
			'findings'     => array_map( static fn ( Finding $f ): array => $f->to_array(), $findings ),
		);
		return (string) json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n";
	}

	/**
	 * @param Finding[] $findings Findings to render.
	 *
	 * @return string
	 */
	public function to_text( array $findings ): string {
		if ( array() === $findings ) {
			return "No dangerous capability grants found.\n";
		}
		$out = sprintf( "%d finding(s):\n", count( $findings ) );
		foreach ( $findings as $finding ) {
			$out .= sprintf(
				"  [%-8s] %s -> %s\n",
				strtoupper( $finding->severity->label() ),
				$finding->role,
				$finding->capability
			);
		}
		return $out;
	}
}
