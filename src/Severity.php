<?php
/**
 * Finding severity, ordered so the CLI can pick the worst.
 *
 * @package Pixypuala\AbilitiesAuditor
 */

declare( strict_types=1 );

namespace Pixypuala\AbilitiesAuditor;

/**
 * How serious an audit finding is.
 */
enum Severity: int {
	case Info     = 0;
	case Low      = 1;
	case Medium   = 2;
	case High     = 3;
	case Critical = 4;

	/**
	 * Lower-case label for output.
	 *
	 * @return string
	 */
	public function label(): string {
		return strtolower( $this->name );
	}
}
