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

use ICanBoogie\OffsetNotDefined;
use ICanBoogie\OffsetNotWritable;
use PHPUnit\Framework\TestCase;

class SupplementalTest extends TestCase
{
	/**
	 * @var Supplemental
	 */
	static private $supplemental;

	static public function setupBeforeClass(): void
	{
		self::$supplemental = get_repository()->supplemental;
	}

	/**
	 * @dataProvider provide_test_sections
	 *
	 * @param string $section
	 * @param string $key
	 */
	public function test_sections($section, $key)
	{
		$section_data = self::$supplemental[$section];
		$this->assertIsArray($section_data);
		$this->assertArrayHasKey($key, $section_data);
	}

	public function test_default_calendar()
	{
		$this->assertArrayHasKey('001', self::$supplemental['calendarPreferenceData']);
	}

	public function provide_test_sections()
	{
		return [

		    [ 'aliases'                , 'languageAlias' ],
			[ 'calendarData'           , 'buddhist' ],
			[ 'calendarPreferenceData' , 'AE' ],
			[ 'characterFallbacks'     , 'U+00AD' ],
			[ 'codeMappings'           , 'AA' ],
            [ 'dayPeriods'             , 'af' ],
			[ 'currencyData'           , 'fractions' ],
			[ 'gender'                 , 'personList' ],
			[ 'languageData'           , 'aa' ],
			[ 'languageGroups'         , 'aav' ],
			[ 'languageMatching'       , 'written' ],
			[ 'likelySubtags'          , 'aa' ],
			[ 'measurementData'        , 'measurementSystem' ],
			[ 'metaZones'              , 'metazoneInfo' ],
			[ 'numberingSystems'       , 'armn' ],
			[ 'ordinals'               , 'af' ],
			[ 'parentLocales'          , 'parentLocale' ],
			[ 'plurals'                , 'af' ],
			[ 'primaryZones'           , 'CL' ],
			[ 'references'             , 'R1000' ],
			[ 'territoryContainment'   , 'EU' ],
			[ 'territoryInfo'          , 'AC' ],
			[ 'timeData'               , 'AD' ],
            [ 'unitPreferenceData'     , 'unitPreferences' ],
			[ 'weekData'               , 'minDays' ],
			[ 'windowsZones'           , 'mapTimezones' ],

		];
	}

    public function test_offset_exists()
    {
        $s = self::$supplemental;

        $this->assertTrue(isset($s['calendarPreferenceData']));
        $this->assertTrue(isset($s['numberingSystems']));
        $this->assertFalse(isset($s[uniqid()]));
    }

	public function test_should_throw_exception_when_getting_undefined_offset()
    {
	    $s = self::$supplemental;
	    $this->expectException(OffsetNotDefined::class);
        $s[uniqid()];
    }

	public function test_should_throw_exception_in_attempt_to_set_offset()
    {
	    $s = self::$supplemental;
	    $this->expectException(OffsetNotWritable::class);
        $s['timeData'] = null;
    }

	public function test_should_throw_exception_in_attempt_to_unset_offset()
    {
	    $s = self::$supplemental;
	    $this->expectException(OffsetNotWritable::class);
        unset($s['timeData']);
    }
}
