<?php
/**
 * The auditor command: gather roles, audit them, render output, pick an exit code.
 *
 * The real work lives in framework-free methods (`normalize_roles()` and `run()`)
 * that take a plain role/capability map and return an {@see AuditResult}. Those
 * are unit-tested directly with no WordPress. The only untestable glue is the
 * thin registration and dispatch that talks to WP-CLI and the admin menu, each
 * guarded by class_exists()/function_exists() so this file loads anywhere.
 *
 * Gathering the *live* roles (get_editable_roles()) is the one step that needs a
 * running WordPress; every other step runs and is verified here.
 *
 * @package Pixypuala\AbilitiesAuditor
 */

declare( strict_types=1 );

namespace Pixypuala\AbilitiesAuditor;

/**
 * Framework-free auditor command with thin WordPress glue.
 */
final class AuditCommand {

	/**
	 * @param Auditor     $auditor Risk auditor.
	 * @param Report      $report  Human/JSON reporter.
	 * @param AuditExport $export  Deterministic export serializer.
	 */
	public function __construct(
		private readonly Auditor $auditor = new Auditor(),
		private readonly Report $report = new Report(),
		private readonly AuditExport $export = new AuditExport(),
	) {}

	/**
	 * Core logic: audit a role map and render the requested format.
	 *
	 * @param array<string, string[]> $roles  Role slug => granted capability slugs.
	 * @param string                  $format One of 'text', 'json', or 'export'.
	 *
	 * @return AuditResult
	 */
	public function run( array $roles, string $format = 'text' ): AuditResult {
		$findings = $this->auditor->audit( $roles );

		$output = match ( $format ) {
			'json'   => $this->report->to_json( $findings ),
			'export' => $this->export->to_json( $roles, $findings ),
			default  => $this->report->to_text( $findings ),
		};

		$exit_code = array() === $findings ? 0 : 1;

		return new AuditResult( $output, $exit_code, $findings );
	}

	/**
	 * Normalise assorted role/capability shapes into a plain slug => list map.
	 *
	 * Accepts the get_editable_roles() shape ({ role: { name, capabilities } }),
	 * a flat granted map ({ role: { cap: true } }), and a plain list
	 * ({ role: [ cap, ... ] }). Only granted (truthy) capabilities are kept.
	 *
	 * @param array<string, mixed> $raw Raw role data.
	 *
	 * @return array<string, string[]>
	 */
	public static function normalize_roles( array $raw ): array {
		$roles = array();
		foreach ( $raw as $role => $value ) {
			$roles[ (string) $role ] = self::extract_capabilities( $value );
		}
		return $roles;
	}

	/**
	 * Pull the granted capability slugs out of a single role's value.
	 *
	 * @param mixed $value Role value in any supported shape.
	 *
	 * @return string[]
	 */
	private static function extract_capabilities( mixed $value ): array {
		if ( is_array( $value ) && isset( $value['capabilities'] ) && is_array( $value['capabilities'] ) ) {
			$value = $value['capabilities'];
		}
		if ( ! is_array( $value ) ) {
			return array();
		}
		if ( array_is_list( $value ) ) {
			return array_values( array_filter( $value, 'is_string' ) );
		}
		return array_keys( array_filter( $value ) );
	}

	/**
	 * Register the WP-CLI command and admin screen. No-op outside WordPress.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public static function register(): void {
		if ( class_exists( '\WP_CLI' ) ) {
			\WP_CLI::add_command( 'abilities-auditor audit', array( self::class, 'invoke_cli' ) );
		}
		if ( function_exists( 'add_action' ) ) {
			add_action( 'admin_menu', array( self::class, 'register_admin_page' ) );
		}
	}

	/**
	 * WP-CLI dispatch: gather live roles, run the core logic, emit, and halt.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string[]              $args       Positional arguments (unused).
	 * @param array<string, string> $assoc_args Flags; supports --format.
	 *
	 * @return void
	 */
	public static function invoke_cli( array $args, array $assoc_args ): void {
		unset( $args );
		$format = (string) ( $assoc_args['format'] ?? 'text' );
		$roles  = self::normalize_roles( function_exists( 'get_editable_roles' ) ? get_editable_roles() : array() );
		$result = ( new self() )->run( $roles, $format );

		if ( class_exists( '\WP_CLI' ) ) {
			\WP_CLI::line( rtrim( $result->output, "\n" ) );
			\WP_CLI::halt( $result->exit_code );
		}
	}

	/**
	 * Register the admin menu page. No-op without the admin API.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public static function register_admin_page(): void {
		if ( ! function_exists( 'add_menu_page' ) ) {
			return;
		}
		add_menu_page(
			'Abilities Permission Audit',
			'Permission Audit',
			'manage_options',
			'abilities-permission-audit',
			array( self::class, 'render_admin_page' )
		);
	}

	/**
	 * Render the admin screen from the live roles. No-op without the admin API.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public static function render_admin_page(): void {
		if ( function_exists( 'current_user_can' ) && ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$roles  = self::normalize_roles( function_exists( 'get_editable_roles' ) ? get_editable_roles() : array() );
		$result = ( new self() )->run( $roles, 'text' );

		echo '<div class="wrap"><h1>Abilities Permission Audit</h1><pre>';
		echo esc_html( $result->output );
		echo '</pre></div>';
	}
}
