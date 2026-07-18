<?php
/**
 * Tests for the framework-free auditor command core.
 *
 * @package Pixypuala\AbilitiesAuditor
 */

declare( strict_types=1 );

namespace Pixypuala\AbilitiesAuditor\Tests;

use PHPUnit\Framework\TestCase;
use Pixypuala\AbilitiesAuditor\AuditCommand;
use Pixypuala\AbilitiesAuditor\Severity;

final class AuditCommandTest extends TestCase {

	/**
	 * @return array<string, string[]>
	 */
	private function roles(): array {
		return array(
			'administrator' => array( 'manage_options', 'edit_plugins' ),
			'contributor'   => array( 'edit_posts', 'manage_options' ),
			'subscriber'    => array( 'read' ),
		);
	}

	public function test_run_flags_findings_and_exits_non_zero(): void {
		$result = ( new AuditCommand() )->run( $this->roles() );

		$this->assertSame( 1, $result->exit_code );
		$this->assertNotEmpty( $result->findings );
		$this->assertStringContainsString( 'contributor', $result->output );
	}

	public function test_run_on_clean_roles_exits_zero(): void {
		$result = ( new AuditCommand() )->run(
			array(
				'author'     => array( 'publish_posts', 'upload_files' ),
				'subscriber' => array( 'read' ),
			)
		);

		$this->assertSame( 0, $result->exit_code );
		$this->assertSame( array(), $result->findings );
		$this->assertStringContainsString( 'No dangerous capability grants found.', $result->output );
	}

	public function test_run_json_format_is_valid_json(): void {
		$result  = ( new AuditCommand() )->run( $this->roles(), 'json' );
		$decoded = json_decode( $result->output, true );

		$this->assertSame( JSON_ERROR_NONE, json_last_error() );
		$this->assertSame( count( $result->findings ), $decoded['findingCount'] );
	}

	public function test_run_export_format_is_deterministic(): void {
		$command   = new AuditCommand();
		$reordered = array(
			'subscriber'    => array( 'read' ),
			'contributor'   => array( 'manage_options', 'edit_posts' ),
			'administrator' => array( 'edit_plugins', 'manage_options' ),
		);

		$this->assertSame(
			$command->run( $this->roles(), 'export' )->output,
			$command->run( $reordered, 'export' )->output
		);
	}

	public function test_normalize_reads_get_editable_roles_shape(): void {
		$raw = array(
			'contributor' => array(
				'name'         => 'Contributor',
				'capabilities' => array(
					'edit_posts'     => true,
					'manage_options' => true,
					'read'           => false,
				),
			),
		);

		$normalized = AuditCommand::normalize_roles( $raw );

		$this->assertSame(
			array( 'contributor' => array( 'edit_posts', 'manage_options' ) ),
			$normalized
		);
	}

	public function test_normalize_reads_flat_map_and_list_shapes(): void {
		$raw = array(
			'editor'     => array(
				'unfiltered_html' => true,
				'edit_pages'      => false,
			),
			'subscriber' => array( 'read' ),
		);

		$this->assertSame(
			array(
				'editor'     => array( 'unfiltered_html' ),
				'subscriber' => array( 'read' ),
			),
			AuditCommand::normalize_roles( $raw )
		);
	}

	public function test_normalized_editor_grant_is_flagged(): void {
		$raw    = array(
			'editor' => array(
				'name'         => 'Editor',
				'capabilities' => array( 'unfiltered_html' => true ),
			),
		);
		$result = ( new AuditCommand() )->run( AuditCommand::normalize_roles( $raw ) );

		$this->assertCount( 1, $result->findings );
		$this->assertSame( Severity::Medium, $result->findings[0]->severity );
	}
}
