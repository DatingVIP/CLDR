<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\CLDR;

use ICanBoogie\DateTime;

class TerritoryTest extends \PHPUnit_Framework_TestCase
{
	public function test_get_info()
	{
		$territory = new Territory(get_repository(), 'FR');
		$this->assertInternalType('array', $territory->info);
	}

	public function test_get_containment()
	{
		$territory = new Territory(get_repository(), 'EU');
		$this->assertInternalType('array', $territory->containment);
	}

	public function test_is_containing()
	{
		$territory = new Territory(get_repository(), 'EU');

		$this->assertTrue($territory->is_containing('FR'));
		$this->assertFalse($territory->is_containing('TA'));
	}

	/**
	 * @dataProvider provide_test_get_currency
	 */
	public function test_get_currency($expected, $territory_code)
	{
		$territory = new Territory(get_repository(), $territory_code);
		$this->assertEquals($expected, $territory->currency);
	}

	public function provide_test_get_currency()
	{
		return array(

			array( 'EUR', 'FR' ),
			array( 'MMK', 'MM' ),
			array( 'USD', 'US' )

		);
	}

	/**
	 * @dataProvider provide_test_currency_at
	 */
	public function test_currency_at($expected, $territory_code, $date)
	{
		$territory = new Territory(get_repository(), $territory_code);
		$this->assertEquals($expected, $territory->currency_at($date));
	}

	public function provide_test_currency_at()
	{
		return array(

			array( 'EUR', 'FR', null ),
			array( 'EUR', 'FR', 'now' ),
			array( 'EUR', 'FR', DateTime::now() ),
			array( 'FRF', 'FR', '1960-01-01' ),
			array( 'FRF', 'FR', '1977-06-06' ),
			array( 'FRF', 'FR', DateTime::from('1977-06-06') ),
			array( 'USS', 'US', DateTime::from('1234-06-06') ),
			array( 'USD', 'US', '1792-01-01' )

		);
	}

	/**
	 * @dataProvider provide_test_get_language
	 */
	public function test_get_language($expected, $territory_code)
	{
		$territory = new Territory(get_repository(), $territory_code);
		$this->assertSame($expected, $territory->language);
	}

	public function provide_test_get_language()
	{
		return array(

			array( 'fr', 'FR' ),
			array( 'en', 'US' ),
			array( 'es', 'ES' )

		);
	}

	public function test_get_population()
	{
		$territory = new Territory(get_repository(), 'ES');
		$this->assertNotEmpty($territory->population);
	}

	/**
	 * @dataProvider provide_test_name_as
	 */
	public function test_name_as($expected, $territory_code, $locale_code)
	{
		$territory = new Territory(get_repository(), $territory_code);
		$this->assertEquals($expected, $territory->name_as($locale_code));
	}

	public function provide_test_name_as()
	{
		return array(

			array( "France",  "FR", "fr" ),
			array( "France",  "FR", "fr-BE" ),
			array( "Francia", "FR", "it" ),
			array( "フランス", "FR", "ja" )

		);
	}

	/**
	 * @dataProvider provide_test_get_name_as
	 */
	public function test_get_name_as($expected, $territory_code, $locale_code)
	{
		$territory = new Territory(get_repository(), $territory_code);
		$this->assertEquals($expected, $territory->{ 'name_as_' . $locale_code });
	}

	public function provide_test_get_name_as()
	{
		return array(

			array( "France",  "FR", "fr" ),
			array( "France",  "FR", "fr_BE" ),
			array( "Francia", "FR", "it" ),
			array( "フランス", "FR", "ja" )

		);
	}

	/**
	 * @dataProvider provide_test_get_property
	 */
	public function test_get_property($expected, $territory_code, $property)
	{
		$territory = new Territory(get_repository(), $territory_code);
		$this->assertEquals($expected, $territory->$property);
	}

	public function provide_test_get_property()
	{
		return array(

			# first_day

			array( "mon",  "FR", 'first_day' ),
			array( "sat",  "EG", 'first_day' ),
			array( "sun",  "BS", 'first_day' ),
			array( "fri",  "MV", 'first_day' ),

			# weekend_start

			array( "sat",  "FR", 'weekend_start' ),
			array( "fri",  "AE", 'weekend_start' ),
			array( "thu",  "AF", 'weekend_start' ),
			array( "sun",  "IN", 'weekend_start' ),

			# weekend_end

			array( "sun",  "FR", 'weekend_end' ),
			array( "sat",  "AE", 'weekend_end' ),
			array( "fri",  "AF", 'weekend_end' )

		);
	}
}
