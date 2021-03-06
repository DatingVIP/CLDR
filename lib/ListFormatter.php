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
 * Formats variable-length lists of things such as "Monday, Tuesday, Friday, and Saturday".
 *
 * @see http://www.unicode.org/reports/tr35/tr35-general.html#ListPatterns
 *
 * @method LocalizedListFormatter localize($locale_code)
 */
class ListFormatter implements Formatter
{
	use AccessorTrait;
	use RepositoryPropertyTrait;
	use LocalizeTrait;

	/**
	 * @param Repository $repository
	 */
	public function __construct(Repository $repository = null)
	{
		$this->repository = $repository;
	}

	/**
	 * Formats a variable-length lists of things.
	 *
	 * @param array $list The list to format.
	 * @param array $list_patterns A list patterns.
	 *
	 * @return string
	 */
	public function __invoke(array $list, array $list_patterns)
	{
		return $this->format($list, $list_patterns);
	}

	/**
	 * Formats a variable-length lists of things.
	 *
	 * @param array $list The list to format.
	 * @param array $list_patterns A list patterns.
	 *
	 * @return string
	 */
	public function format(array $list, array $list_patterns)
	{
		$list = array_values($list);

		switch (count($list))
		{
			case 0:

				return "";

			case 1:

				return (string) current($list);

			case 2:

				return $this->format_pattern($list_patterns[2], $list[0], $list[1]);

			default:

				$n = count($list) - 1;
				$v1 = $list[$n];

				for ($i = $n - 1 ; $i > -1 ; $i--)
				{
					$v0 = $list[$i];

					if ($i == 0)
					{
						$pattern = $list_patterns['start'];
					}
					else if ($i + 1 == $n)
					{
						$pattern = $list_patterns['end'];
					}
					else
					{
						$pattern = $list_patterns['middle'];
					}

					$v1 = $this->format_pattern($pattern, $v0, $v1);
				}

				return $v1;
		}
	}

	/**
	 * @param string $pattern
	 * @param string $v0
	 * @param string $v1
	 *
	 * @return string
	 */
	private function format_pattern($pattern, $v0, $v1)
	{
		return strtr($pattern, [

			'{0}' => $v0,
			'{1}' => $v1

		]);
	}
}
