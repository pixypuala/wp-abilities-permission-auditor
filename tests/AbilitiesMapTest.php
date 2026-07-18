<?php
/**
 * Tests for the Abilities-API-style mapper.
 *
 * @package Pixypuala\AbilitiesAuditor
 */

declare( strict_types=1 );

namespace Pixypuala\AbilitiesAuditor\Tests;

use PHPUnit\Framework\TestCase;
use Pixypuala\AbilitiesAuditor\AbilitiesMap;
use Pixypuala\AbilitiesAuditor\Auditor;

final class AbilitiesMapTest extends TestCase {

	/**
	 * @return array<string, string[]>
	 */
	private function roles(): array {
		return array(
			'contributor' => array( 'edit_posts', 'manage_options' ),
			'subscriber'  => array( 'read' ),
		);
	}

	public function test_every_grant_becomes_one_ability(): void {
		$findings  = ( new Auditor() )->audit( $this->roles() );
		$abilities = ( new AbilitiesMap() )->to_abilities( $this->roles(), $findings );

		$this->assertCount( 3, $abilities );
	}

	public function test_flagged_grant_carries_severity(): void {
		$findings  = ( new Auditor() )->audit( $this->roles() );
		$abilities = ( new AbilitiesMap() )->to_abilities( $this->roles(), $findings );

		$flagged = array_values(
			array_filter( $abilities, static fn ( array $a ): bool => $a['meta']['flagged'] )
		);

		$this->assertCount( 1, $flagged );
		$this->assertSame( 'contributor', $flagged[0]['meta']['role'] );
		$this->assertSame( 'manage_options', $flagged[0]['meta']['capability'] );
		$this->assertSame( 'high', $flagged[0]['meta']['severity'] );
		$this->assertSame( 'abilities-auditor/contributor.manage_options', $flagged[0]['name'] );
		$this->assertSame( AbilitiesMap::CATEGORY, $flagged[0]['category'] );
	}

	public function test_clean_grant_has_no_severity(): void {
		$abilities = ( new AbilitiesMap() )->to_abilities(
			array( 'subscriber' => array( 'read' ) ),
			array()
		);

		$this->assertFalse( $abilities[0]['meta']['flagged'] );
		$this->assertNull( $abilities[0]['meta']['severity'] );
	}

	public function test_output_is_sorted_by_name(): void {
		$abilities = ( new AbilitiesMap() )->to_abilities( $this->roles(), array() );
		$names     = array_column( $abilities, 'name' );
		$sorted    = $names;
		sort( $sorted, SORT_STRING );

		$this->assertSame( $sorted, $names );
	}

	public function test_duplicate_capabilities_collapse(): void {
		$abilities = ( new AbilitiesMap() )->to_abilities(
			array( 'editor' => array( 'read', 'read', 'edit_posts' ) ),
			array()
		);

		$this->assertCount( 2, $abilities );
	}
}
