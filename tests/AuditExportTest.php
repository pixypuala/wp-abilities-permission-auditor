<?php
/**
 * Tests for the deterministic JSON export serializer.
 *
 * @package Pixypuala\AbilitiesAuditor
 */

declare( strict_types=1 );

namespace Pixypuala\AbilitiesAuditor\Tests;

use PHPUnit\Framework\TestCase;
use Pixypuala\AbilitiesAuditor\Auditor;
use Pixypuala\AbilitiesAuditor\AuditExport;

final class AuditExportTest extends TestCase {

	/**
	 * @return array<string, string[]>
	 */
	private function roles(): array {
		return array(
			'subscriber'    => array( 'read' ),
			'contributor'   => array( 'manage_options', 'edit_posts' ),
			'editor'        => array( 'unfiltered_html', 'edit_others_posts' ),
			'administrator' => array( 'edit_users', 'manage_options', 'edit_plugins' ),
		);
	}

	public function test_schema_version_is_present(): void {
		$export   = new AuditExport();
		$findings = ( new Auditor() )->audit( $this->roles() );
		$document = $export->to_array( $this->roles(), $findings );
		$this->assertSame( AuditExport::SCHEMA_VERSION, $document['schemaVersion'] );
	}

	public function test_role_keys_and_capabilities_are_sorted(): void {
		$export   = new AuditExport();
		$document = $export->to_array( $this->roles(), array() );

		$this->assertSame(
			array( 'administrator', 'contributor', 'editor', 'subscriber' ),
			array_keys( $document['roles'] )
		);
		$this->assertSame(
			array( 'edit_plugins', 'edit_users', 'manage_options' ),
			$document['roles']['administrator']
		);
	}

	public function test_output_is_deterministic_regardless_of_input_order(): void {
		$export = new AuditExport();

		$reordered = array(
			'editor'        => array( 'edit_others_posts', 'unfiltered_html' ),
			'administrator' => array( 'edit_plugins', 'manage_options', 'edit_users' ),
			'subscriber'    => array( 'read' ),
			'contributor'   => array( 'edit_posts', 'manage_options' ),
		);

		$findings_a = ( new Auditor() )->audit( $this->roles() );
		$findings_b = ( new Auditor() )->audit( $reordered );

		$this->assertSame(
			$export->to_json( $this->roles(), $findings_a ),
			$export->to_json( $reordered, $findings_b )
		);
	}

	public function test_json_snapshot_of_known_input(): void {
		$export   = new AuditExport();
		$roles    = array(
			'contributor' => array( 'manage_options', 'edit_plugins' ),
		);
		$findings = ( new Auditor() )->audit( $roles );

		$expected = <<<'JSON'
			{
			    "schemaVersion": "1.0.0",
			    "roleCount": 1,
			    "findingCount": 2,
			    "roles": {
			        "contributor": [
			            "edit_plugins",
			            "manage_options"
			        ]
			    },
			    "findings": [
			        {
			            "role": "contributor",
			            "capability": "edit_plugins",
			            "severity": "critical",
			            "message": "Role \"contributor\" holds the dangerous capability \"edit_plugins\"; normally only administrator should."
			        },
			        {
			            "role": "contributor",
			            "capability": "manage_options",
			            "severity": "high",
			            "message": "Role \"contributor\" holds the dangerous capability \"manage_options\"; normally only administrator should."
			        }
			    ]
			}
			JSON;

		$this->assertSame( $expected . "\n", $export->to_json( $roles, $findings ) );
	}

	public function test_round_trip_decodes_to_the_same_document(): void {
		$export   = new AuditExport();
		$findings = ( new Auditor() )->audit( $this->roles() );
		$document = $export->to_array( $this->roles(), $findings );
		$json     = $export->to_json( $this->roles(), $findings );

		$this->assertSame( JSON_ERROR_NONE, json_last_error() );
		$this->assertSame( $document, json_decode( $json, true ) );
	}
}
