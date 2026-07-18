<?php
/**
 * The audit policy: which capabilities are dangerous on non-administrator roles.
 *
 * These are core WordPress capabilities that grant control over code execution,
 * user accounts, or site configuration. On any role below administrator they are
 * a privilege-escalation or site-takeover risk, so each maps to a severity.
 * Keeping the policy data-driven makes it easy to extend and to unit-test.
 *
 * @package Pixypuala\AbilitiesAuditor
 */

declare( strict_types=1 );

namespace Pixypuala\AbilitiesAuditor;

/**
 * Capability risk policy.
 */
final class Policy {

	/**
	 * Capability => severity when held by a non-administrator role.
	 *
	 * @return array<string, Severity>
	 */
	public function dangerous_capabilities(): array {
		return array(
			// Arbitrary code execution / site takeover.
			'edit_plugins'      => Severity::Critical,
			'edit_themes'       => Severity::Critical,
			'edit_files'        => Severity::Critical,
			'install_plugins'   => Severity::Critical,
			'install_themes'    => Severity::Critical,
			'update_plugins'    => Severity::High,
			'update_themes'     => Severity::High,
			'update_core'       => Severity::High,
			'activate_plugins'  => Severity::High,

			// Account control / privilege escalation.
			'edit_users'        => Severity::Critical,
			'delete_users'      => Severity::Critical,
			'create_users'      => Severity::High,
			'promote_users'     => Severity::High,

			// Configuration control.
			'manage_options'    => Severity::High,
			'manage_network'    => Severity::Critical,

			// Content-injection risk (unfiltered HTML/JS).
			'unfiltered_html'   => Severity::Medium,
			'unfiltered_upload' => Severity::Medium,
		);
	}

	/**
	 * The role name treated as the legitimate holder of dangerous capabilities.
	 *
	 * @return string
	 */
	public function privileged_role(): string {
		return 'administrator';
	}
}
