<?php

namespace Diff\ArrayComparer;

use Diff\Comparer\ValueComparer;

/**
 * Computes the difference between two arrays by comparing elements with
 * a ValueComparer.
 *
 * Quantity matters: [42, 42] and [42] are different
 *
 * @since 0.8
 *
 * @file
 * @ingroup Diff
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StrategicArrayComparer implements ArrayComparer {

	protected $valueComparer;

	public function __construct( ValueComparer $valueComparer ) {
		$this->valueComparer = $valueComparer;
	}

	/**
	 * @see ArrayComparer::diffArrays
	 *
	 * @since 0.8
	 *
	 * @param array $arrayOne
	 * @param array $arrayTwo
	 *
	 * @return array
	 */
	public function diffArrays( array $arrayOne, array $arrayTwo ) {
		$notInTwo = array();

		foreach ( $arrayOne as $element ) {
			$valueOffset = $this->arraySearch( $element, $arrayTwo );

			if ( $valueOffset === false ) {
				$notInTwo[] = $element;
				continue;
			}

			unset( $arrayTwo[$valueOffset] );
		}

		return $notInTwo;
	}

	/**
	 * @since 0.8
	 *
	 * @param string|int $needle
	 * @param array $haystack
	 *
	 * @return bool|int|string
	 */
	protected function arraySearch( $needle, array $haystack ) {
		foreach ( $haystack as $valueOffset => $thing ) {
			$areEqual = $this->valueComparer->valuesAreEqual( $needle, $thing );

			if ( $areEqual ) {
				return $valueOffset;
			}
		}

		return false;
	}

}
