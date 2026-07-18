<?php
/**
 * The outcome of running the auditor command's core logic.
 *
 * Carries the rendered output and the process/exit code so the framework glue
 * (WP-CLI, an admin screen, or the standalone bin) only has to emit the output
 * and honour the code. Keeping this immutable and framework-free means the
 * command's real work is fully unit-testable without WordPress.
 *
 * @package Pixypuala\AbilitiesAuditor
 */

declare( strict_types=1 );

namespace Pixypuala\AbilitiesAuditor;

/**
 * Immutable result of a command run.
 */
final class AuditResult {

	/**
	 * @param string    $output    Rendered report or export, ready to emit.
	 * @param int       $exit_code Process exit code: 0 when clean, 1 on any finding.
	 * @param Finding[] $findings  Findings the run produced, most-severe first.
	 */
	public function __construct(
		public readonly string $output,
		public readonly int $exit_code,
		public readonly array $findings,
	) {}
}
