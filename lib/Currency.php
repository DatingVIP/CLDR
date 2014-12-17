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
 * A currency.
 *
 * @package ICanBoogie\CLDR
 *
 * @property-read string $code The ISO code of the currency.
 * @property-read int $digits
 * @property-read int $rounding
 * @property-read int $cash_digits
 * @property-read int $cash_rounding
 */
class Currency
{
	/**
	 * @var Repository
	 */
	protected $repository;

	/**
	 * @var string
	 */
	protected $code;

	/**
	 * @param Repository $repository
	 * @param string $code Currency ISO code.
	 */
	public function __construct(Repository $repository, $code)
	{
		$this->repository = $repository;
		$this->code = $code;
	}

	public function __get($property)
	{
		switch ($property)
		{
			case 'code':

				return $this->code;

			case 'digits':
			case 'rounding':
			case 'cash_digits':
			case 'cash_rounding':

				$data = $this->$repository->supplemental['currencyData'][$this->code];
				$offset = '_' . $property;

				return isset($data[$offset]) ? (int) $data[$offset] : null;
		}

		throw new PropertyNotDefined(array( $property, $this ));
	}

	public function __toString()
	{
		return $this->code;
	}

	/**
	 * Localize the currency.
	 *
	 * @param $locale_code
	 *
	 * @return LocalizedCurrency
	 */
	public function localize($locale_code)
	{
		return $this->repository->locales[$locale_code]->localize($this);
	}
}