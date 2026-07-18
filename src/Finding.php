<?php
/**
 * A single audit finding: a role holds a capability it should not.
 *
 * @package Pixypuala\AbilitiesAuditor
 */

declare( strict_types=1 );

namespace Pixypuala\AbilitiesAuditor;

/**
 * Immutable audit finding.
 */
final class Finding {

	/**
	 * @param string   $role       Role slug the finding applies to.
	 * @param string   $capability Capability at issue.
	 * @param Severity $severity   How serious it is.
	 * @param string   $message    Human-readable explanation.
	 */
	public function __construct(
		public readonly string $role,
		public readonly string $capability,
		public readonly Severity $severity,
		public readonly string $message,
	) {}

	/**
	 * @return array<string, mixed>
	 */
	public function to_array(): array {
		return array(
			'role'       => $this->role,
			'capability' => $this->capability,
			'severity'   => $this->severity->label(),
			'message'    => $this->message,
		);
	}
}
