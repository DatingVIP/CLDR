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

class LocaleCollectionTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var LocaleCollection
	 */
	static private $collection;

	static public function setupBeforeClass()
	{
		self::$collection = new LocaleCollection(get_repository());
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function test_offsetExists()
	{
		self::$collection->offsetExists('fr');
	}

	/**
	 * @expectedException \ICanBoogie\OffsetNotWritable
	 */
	public function test_offsetSet()
	{
		self::$collection['fr'] = null;
	}

	/**
	 * @expectedException \ICanBoogie\OffsetNotWritable
	 */
	public function test_offsetUnset()
	{
		unset(self::$collection['fr']);
	}

	public function test_existing_locale()
	{
		$this->assertInstanceOf('ICanBoogie\CLDR\Locale', self::$collection['fr']);
		$this->assertInstanceOf('ICanBoogie\CLDR\Locale', self::$collection['en']);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function test_empty_identifier()
	{
		self::$collection[''];
	}
}
