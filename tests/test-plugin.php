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

	// Check for PHP errors
	function test_general() {
		$gwcc = genesis_widget_column_classes();
		$gwcc->get_links();
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
			// Single quotes.
			array(
				'start'  => "<div class='test'>",
				'data'   => 'one two three',
				'result' => "<div class='test one two three'>",
			),
			array(
				'start'  => "<div class='test one two'>",
				'data'   => 'one one two three',
				'result' => "<div class='test one two three'>",
			),
			// Multiple elements (only first attribute found should be modified).
			array(
				'start'  => '<div class="test one one two"><p class="test"></p>',
				'data'   => 'one two three',
				'result' => '<div class="test one two three"><p class="test"></p>',
			),
			// @todo Should this happen?
			array(
				'start'  => '<div><p class="test"></p>',
				'data'   => 'one two three',
				'result' => '<div><p class="test one two three"></p>',
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

	// Check widget_update() method
	function test_widget_update_callback() {
		$gwcc = genesis_widget_column_classes();

		$tests = array(
			// Column classes and a first column.
			array(
				'start'  => array(),
				'data'   => array(
					'column-classes' => 'test',
				    'column-classes-first' => '1',
				),
				'result' => array(
					'column-classes' => 'test',
					'column-classes-first' => true,
				),
			),
			// Not a first column.
			array(
				'start'  => array(),
				'data'   => array(
					'column-classes' => 'test',
					'column-classes-first' => '',
				),
				'result' => array(
					'column-classes' => 'test',
				),
			),
			array(
				'start'  => array(),
				'data'   => array(
					'column-classes-first' => false,
				),
				'result' => array(),
			),
			array(
				'start'  => array(),
				'data'   => array(
					'column-classes-first' => '',
				),
				'result' => array(),
			),
			// First column only.
			array(
				'start'  => array(),
				'data'   => array(
					'column-classes' => '',
					'column-classes-first' => 'anything since it checks for ! empty',
				),
				'result' => array(
					'column-classes-first' => true,
				),
			),
			// Empty data.
			array(
				'start'  => array(),
				'data'   => array(
					'column-classes' => '',
					'column-classes-first' => array(),
				),
				'result' => array(),
			),
			// Remove empty data.
			array(
				'start'  => array(
					'column-classes' => 'test',
					'column-classes-first' => true,
				),
				'data'   => array(),
				'result' => array(),
			),
			array(
				'start'  => array(
					'column-classes' => 'test',
					'column-classes-first' => true,
				),
				'data'   => array(
					'column-classes' => '',
				),
				'result' => array(),
			),
		);

		// Run tests
		foreach ( $tests as $test ) {
			$this->assertEquals( $test['result'], $gwcc->filter_widget_update_callback( $test['start'], $test['data'] ) );
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
