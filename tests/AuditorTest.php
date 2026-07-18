<?php
/**
 * Tests for the capability auditor.
 *
 * @package Pixypuala\AbilitiesAuditor
 */

declare( strict_types=1 );

namespace Pixypuala\AbilitiesAuditor\Tests;

use PHPUnit\Framework\TestCase;
use Pixypuala\AbilitiesAuditor\Auditor;
use Pixypuala\AbilitiesAuditor\Report;
use Pixypuala\AbilitiesAuditor\Severity;

final class AuditorTest extends TestCase {

	private function roles(): array {
		return array(
			'administrator' => array( 'manage_options', 'edit_plugins', 'edit_users' ),
			'editor'        => array( 'edit_others_posts', 'unfiltered_html' ),
			'contributor'   => array( 'edit_posts', 'manage_options' ),
			'subscriber'    => array( 'read' ),
		);
	}

	public function test_administrator_grants_are_not_flagged(): void {
		$findings = ( new Auditor() )->audit( $this->roles() );
		foreach ( $findings as $finding ) {
			$this->assertNotSame( 'administrator', $finding->role );
		}
	}

	public function test_contributor_with_manage_options_is_flagged_high(): void {
		$findings = ( new Auditor() )->audit( $this->roles() );
		$match    = array_values(
			array_filter(
				$findings,
				static fn ( $f ): bool => 'contributor' === $f->role && 'manage_options' === $f->capability
			)
		);
		$this->assertCount( 1, $match );
		$this->assertSame( Severity::High, $match[0]->severity );
	}

	public function test_editor_unfiltered_html_is_flagged_medium(): void {
		$findings = ( new Auditor() )->audit( $this->roles() );
		$match    = array_filter( $findings, static fn ( $f ): bool => 'unfiltered_html' === $f->capability );
		$this->assertNotEmpty( $match );
		$this->assertSame( Severity::Medium, array_values( $match )[0]->severity );
	}

	public function test_clean_roles_produce_no_findings(): void {
		$findings = ( new Auditor() )->audit(
			array(
				'subscriber' => array( 'read' ),
				'author'     => array( 'publish_posts', 'upload_files' ),
			)
		);
		$this->assertSame( array(), $findings );
	}

	public function test_findings_are_sorted_most_severe_first(): void {
		$findings = ( new Auditor() )->audit(
			array(
				'contributor' => array( 'unfiltered_html', 'edit_plugins' ), // medium + critical
			)
		);
		$this->assertSame( Severity::Critical, $findings[0]->severity );
	}

	public function test_worst_returns_highest_severity(): void {
		$auditor  = new Auditor();
		$findings = $auditor->audit( array( 'contributor' => array( 'edit_plugins', 'unfiltered_html' ) ) );
		$this->assertSame( Severity::Critical, $auditor->worst( $findings ) );
		$this->assertNull( $auditor->worst( array() ) );
	}

	public function test_report_json_is_valid(): void {
		$findings = ( new Auditor() )->audit( $this->roles() );
		$json     = ( new Report() )->to_json( $findings );
		$decoded  = json_decode( $json, true );
		$this->assertSame( JSON_ERROR_NONE, json_last_error() );
		$this->assertSame( count( $findings ), $decoded['findingCount'] );
	}
}
