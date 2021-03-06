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

use ICanBoogie\Accessor\AccessorTrait;

/**
 * Representation of a number pattern.
 *
 * @property-read array $format
 * @property-read string $positive_prefix Prefix to positive number.
 * @property-read string $positive_suffix Suffix to positive number.
 * @property-read string $negative_prefix Prefix to negative number.
 * @property-read string $negative_suffix Suffix to negative number.
 * @property-read int $multiplier 100 for percent, 1000 for per mille.
 * @property-read int $decimal_digits The number of required digits after decimal point. The
 * string is padded with zeros if there is not enough digits. `-1` means the decimal point should
 * be dropped.
 * @property-read int $max_decimal_digits The maximum number of digits after decimal point.
 * Additional digits will be truncated.
 * @property-read int $integer_digits The number of required digits before decimal point. The
 * string is padded with zeros if there is not enough digits.
 * @property-read int $group_size1 The primary grouping size. `0` means no grouping.
 * @property-read int $group_size2 The secondary grouping size. `0` means no secondary grouping
 */
class NumberPattern
{
	use AccessorTrait;

	/**
	 * @var NumberPattern[]
	 */
	static private $instances = [];

	/**
	 * @param string $pattern
	 *
	 * @return NumberPattern
	 */
	static public function from($pattern)
	{
		if (isset(self::$instances[$pattern]))
		{
			return self::$instances[$pattern];
		}

		$format = NumberPatternParser::parse($pattern);

		return self::$instances[$pattern] = new static($pattern, $format);
	}

	/**
	 * @var string
	 */
	private $pattern;

	/**
	 * @var array
	 */
	private $format;

	/**
	 * @return array
	 */
	protected function get_format()
	{
		return $this->format;
	}

	/**
	 * @param string $pattern
	 * @param array $format
	 */
	private function __construct($pattern, array $format)
	{
		$this->pattern = $pattern;
		$this->format = $format;
	}

	/**
	 * @inheritdoc
	 */
	public function __get($property)
	{
		if (array_key_exists($property, $this->format))
		{
			return $this->format[$property];
		}

		return $this->accessor_get($property);
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->pattern;
	}

	/**
	 * Parse a number according to the pattern and return its integer and decimal parts.
	 *
	 * @param number $number
	 *
	 * @return array An array made with the integer and decimal parts of the number.
	 */
	public function parse_number($number)
	{
		$number = abs($number * $this->multiplier);

		if ($this->max_decimal_digits >= 0)
		{
			$number = round($number, $this->max_decimal_digits);
		}

		$number = "$number";

		if (($pos = strpos($number, '.')) !== false)
		{
			return [ substr($number, 0, $pos), substr($number, $pos + 1) ];
		}

		return [ $number, '' ];
	}

	/**
	 * Formats integer according to group pattern.
	 *
	 * @param int $integer
	 * @param string $group_symbol
	 *
	 * @return string
	 */
	public function format_integer_with_group($integer, $group_symbol)
	{
		$integer = str_pad($integer, $this->integer_digits, '0', STR_PAD_LEFT);
		$group_size1 = $this->group_size1;

		if ($group_size1 < 1 || strlen($integer) <= $this->group_size1)
		{
			return $integer;
		}

		$group_size2 = $this->group_size2;

		$str1 = substr($integer, 0, -$group_size1);
		$str2 = substr($integer, -$group_size1);
		$size = $group_size2 > 0 ? $group_size2 : $group_size1;
		$str1 = str_pad($str1, (int) ((strlen($str1) + $size - 1) / $size) * $size, ' ', STR_PAD_LEFT);

		return ltrim(implode($group_symbol, str_split($str1, $size))) . $group_symbol . $str2;
	}

	/**
	 * Formats an integer with a decimal.
	 *
	 * @param string|int $integer An integer, or a formatted integer as returned by
	 * {@link format_integer_with_group}.
	 * @param string $decimal
	 * @param string $decimal_symbol
	 *
	 * @return string
	 */
	public function format_integer_with_decimal($integer, $decimal, $decimal_symbol)
	{
		$decimal = $decimal ? (string) $decimal : '';

		if ($this->decimal_digits > strlen($decimal))
		{
			$decimal = str_pad($decimal, $this->decimal_digits, '0');
		}

		if (strlen($decimal))
		{
			$decimal = $decimal_symbol . $decimal;
		}

		return "$integer" . $decimal;
	}
}
