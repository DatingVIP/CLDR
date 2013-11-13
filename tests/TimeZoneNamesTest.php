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

class TimeZoneNamesTest extends \PHPUnit_Framework_TestCase
{
	static private $tzn;

	static public function setupBeforeClass()
	{
		$locale = get_repository()->locales['fr'];

		self::$tzn = new TimeZoneNames($locale, $locale['timeZoneNames']);
	}

	public function test_get_locale()
	{
		$this->assertInstanceOf('ICanBoogie\CLDR\Locale', self::$tzn->locale);
	}

	/**
	 * @expectedException ICanBoogie\PropertyNotDefined
	 */
	public function test_get_undefined()
	{
		self::$tzn->undefined;
	}

	/**
	 * @dataProvider provide_test_resolve_name
	 */
	public function test_resolve_name($time_zone_id, $expected)
	{
		$this->assertEquals($expected, self::$tzn->resolve_name($time_zone_id));
	}

	public function provide_test_resolve_name()
	{
		return array
		(
			array('America/Indiana/Tell_City', "Tell City [Indiana]"),
			array('America/North_Dakota/New_Salem', "New Salem [Dakota du Nord]"),
			array('Asia/Saigon', "Hô-Chi-Minh-Ville"),
			array('Europe/Paris', "Paris")
		);
	}
}