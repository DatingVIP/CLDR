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

use ICanBoogie\PropertyNotDefined;

/**
 * Representation of the `timeZoneNames` array.
 *
 * @property-read Locale $locale The locale the data is defined in.
 */
class TimeZoneNames extends \ArrayObject
{
	protected $locale;

	public function __construct(Locale $locale, array $data)
	{
		$this->locale = $locale;

		parent::__construct($data);
	}

	public function __get($property)
	{
		if ($property === 'locale')
		{
			return $this->locale;
		}

		throw new PropertyNotDefined(array($property, $this));
	}

	public function resolve_name($time_zone_id)
	{
		$parts = explode('/', $time_zone_id);
		$array = $this['zone'];

		foreach ($parts as $part)
		{
			$array = $array[$part];
		}

		return $array['exemplarCity'];
	}
}