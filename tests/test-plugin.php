<?php
/**
 * Genesis Widget Column Classes - Unit tests
 *
 * @author  Jory Hogeveen <info@keraweb.nl>
 * @package Genesis_Widget_Column_Classes
 */

class PluginTest extends WP_UnitTestCase {

	// Check that that activation doesn't break
	function test_plugin_activated() {
		$this->assertTrue( is_plugin_active( TEST_GWCC_PLUGIN_PATH ) );
	}

	// Check append_to_attribute() method
	function test_append_to_attribute() {
		$gwcc = genesis_widget_column_classes();

		$tests = array(
			array(
				'start'  => '<div class="test">',
				'data'   => 'one two three',
				'result' => '<div class="test one two three">',
			),
			array(
				'start'  => '<div class="test one two">',
				'data'   => 'one two three',
				'result' => '<div class="test one two three">',
			),
			array(
				'start'  => '<div class="test one one two">',
				'data'   => 'one two three',
				'result' => '<div class="test one two three">',
			),
			array(
				'start'  => '<div class="test one two">',
				'data'   => 'one one two three',
				'result' => '<div class="test one two three">',
			),
		);

		// Unique result tests.
		foreach ( $tests as $test ) {
			$this->assertEquals( $test['result'], $gwcc->append_to_attribute( $test['start'], 'class', $test['data'], true ) );
		}

		unset( $tests[0] );

		foreach ( $tests as $test ) {
			$this->assertNotEquals( $test['result'], $gwcc->append_to_attribute( $test['start'], 'class', $test['data'], false ) );
		}
	}

	// Check column classes filter.
	function test_column_classes() {
		$gwcc = genesis_widget_column_classes();
		add_filter( 'genesis_widget_column_classes', array( $this, 'gwcc_filter_add_column_classes' ) );
		$this->assertArrayHasKey( 'column-test', $gwcc->get_column_classes() );
	}

	// Helper function for `genesis_widget_column_classes` filter
	function gwcc_filter_add_column_classes( $classes ) {
		$classes['column-test'] = 'column-test';
		return $classes;
	}
}
