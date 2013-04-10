<?php

namespace Diff\Tests;

use Diff\DiffOp;

/**
 * Base test class for the Diff\DiffOp deriving classes.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @since 0.1
 *
 * @ingroup DiffTest
 *
 * @group Diff
 * @group DiffOp
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Daniel Kinzler
 */
abstract class DiffOpTest extends DiffTestCase {

	/**
	 * Returns the name of the concrete class tested by this test.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public abstract function getClass();

	/**
	 * First element can be a boolean indication if the successive values are valid,
	 * or a string indicating the type of exception that should be thrown (ie not valid either).
	 *
	 * @since 0.1
	 *
	 * @return array
	 */
	public abstract function constructorProvider();

	/**
	 * Creates and returns a new instance of the concrete class.
	 *
	 * @since 0.1
	 *
	 * @return mixed
	 */
	public function newInstance() {
		$reflector = new \ReflectionClass( $this->getClass() );
		$args = func_get_args();
		$instance = $reflector->newInstanceArgs( $args );
		return $instance;
	}

	/**
	 * @since 0.1
	 *
	 * @return array [instance, constructor args]
	 */
	public function instanceProvider() {
		$phpFails = array( $this, 'newInstance' );

		return array_filter( array_map(
			function( array $args ) use ( $phpFails ) {
				$isValid = array_shift( $args ) === true;

				if ( $isValid ) {
					return array( call_user_func_array( $phpFails, $args ), $args );
				}
				else {
					return false;
				}
			},
			$this->constructorProvider()
		), 'is_array' );
	}

	/**
	 * @dataProvider constructorProvider
	 *
	 * @since 0.1
	 */
	public function testConstructor() {
		$args = func_get_args();

		$valid = array_shift( $args );
		$pokemons = null;

		try {
			$dataItem = call_user_func_array( array( $this, 'newInstance' ), $args );
			$this->assertInstanceOf( $this->getClass(), $dataItem );
		}
		catch ( \Exception $pokemons ) {
			if ( $valid === true ) {
				throw $pokemons;
			}

			if ( is_string( $valid ) ) {
				$this->assertEquals( $valid, get_class( $pokemons ) );
			}
			else {
				$this->assertFalse( $valid );
			}
		}
	}




	/**
	 * @dataProvider instanceProvider
	 */
	public function testIsAtomic( DiffOp $diffOp ) {
		$this->assertInternalType( 'boolean', $diffOp->isAtomic() );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetType( DiffOp $diffOp ) {
		$this->assertInternalType( 'string', $diffOp->getType() );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testSerialization( DiffOp $diffOp ) {
		$serialization = serialize( $diffOp );
		$unserialization = unserialize( $serialization );
		$this->assertEquals( $diffOp, $unserialization );
		$this->assertEquals( serialize( $diffOp ), serialize( $unserialization ) );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testCount( DiffOp $diffOp ) {
		if ( $diffOp->isAtomic() ) {
			$this->assertEquals( 1, count( $diffOp ) );
		}
		else {
			$count = 0;

			/**
			 * @var DiffOp $childOp
			 */
			foreach ( $diffOp as $childOp ) {
				$count += $childOp->count();
			}

			$this->assertEquals( $count, count( $diffOp ) );
		}
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testToArray( DiffOp $diffOp ) {
		$array = $diffOp->toArray();

		$this->assertInternalType( 'array', $array );
		$this->assertArrayHasKey( 'type', $array );
		$this->assertInternalType( 'string', $array['type'] );
		$this->assertEquals( $diffOp->getType(), $array['type'] );
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testToArrayWithConversion( DiffOp $diffOp ) {
		$array = $diffOp->toArray( 'Diff\Tests\DiffOpTestDummy::arrayalize' );

		$this->assertInternalType( 'array', $array );
	}

}