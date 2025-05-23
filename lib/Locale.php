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

use InvalidArgumentException;
use LogicException;

/**
 * Representation of a locale.
 *
 * @property-read Repository $repository The repository provided during construct.
 * @property-read string $code The ISO code of the locale.
 * @property-read string $language The language code.
 * @property-read CalendarCollection $calendars The calendar collection of the locale.
 * @property-read Calendar $calendar The preferred calendar for this locale.
 * @property-read Numbers $numbers
 * @property-read LocalizedNumberFormatter $number_formatter
 * @property-read LocalizedCurrencyFormatter $currency_formatter
 * @property-read LocalizedListFormatter $list_formatter
 * @property-read ContextTransforms $context_transforms
 * @property-read Units $units
 */
class Locale extends AbstractSectionCollection
{
	/**
	 * @var array<string, string>
	 *     Where _key_ is a property and _value_ its CLDR path.
	 */
	static private $available_sections = [

		'ca-buddhist'            => 'dates/calendars/buddhist',
		'ca-chinese'             => 'dates/calendars/chinese',
		'ca-coptic'              => 'dates/calendars/coptic',
		'ca-dangi'               => 'dates/calendars/dangi',
		'ca-ethiopic'            => 'dates/calendars/ethiopic',
		'ca-generic'             => 'dates/calendars/generic',
		'ca-gregorian'           => 'dates/calendars/gregorian',
		'ca-hebrew'              => 'dates/calendars/hebrew',
		'ca-indian'              => 'dates/calendars/indian',
		'ca-islamic'             => 'dates/calendars/islamic',
		'ca-japanese'            => 'dates/calendars/japanese',
		'ca-persian'             => 'dates/calendars/persian',
		'ca-roc'                 => 'dates/calendars/roc',
		'characters'             => 'characters',
		'contextTransforms'      => 'contextTransforms',
		'currencies'             => 'numbers/currencies',
		'dateFields'             => 'dates/fields',
		'delimiters'             => 'delimiters',
		'languages'              => 'localeDisplayNames/languages',
		'layout'                 => 'layout',
		'listPatterns'           => 'listPatterns',
		'localeDisplayNames'     => 'localeDisplayNames',
		'measurementSystemNames' => 'localeDisplayNames/measurementSystemNames',
		'numbers'                => 'numbers',
		'posix'                  => 'posix',
		'scripts'                => 'localeDisplayNames/scripts',
		'territories'            => 'localeDisplayNames/territories',
		'timeZoneNames'          => 'dates/timeZoneNames',
		'units'                  => 'units',
		'variants'               => 'localeDisplayNames/variants'

	];

	use CodePropertyTrait;

	/**
	 * @param string $code The ISO code of the locale.
	 */
	public function __construct(Repository $repository, string $code)
	{
		if (!$code)
		{
			throw new InvalidArgumentException("Locale identifier cannot be empty.");
		}

		parent::__construct($repository, "main/$code", self::$available_sections);

		$this->code = $code;
	}

	protected function get_language(): string
	{
		[ $language ] = explode('-', $this->code, 2);

		return $language;
	}

	protected function lazy_get_calendars(): CalendarCollection
	{
		return new CalendarCollection($this);
	}

	protected function lazy_get_calendar(): Calendar
	{
		return $this->calendars['gregorian']; // TODO-20131101: use preferred data
	}

	protected function lazy_get_numbers(): Numbers
	{
		return new Numbers($this, $this['numbers']);
	}

	protected function lazy_get_number_formatter(): LocalizedNumberFormatter
	{
		return $this->localize($this->repository->number_formatter);
	}

	protected function lazy_get_currency_formatter(): LocalizedCurrencyFormatter
	{
		return $this->localize($this->repository->currency_formatter);
	}

	protected function lazy_get_list_formatter(): LocalizedListFormatter
	{
		return $this->localize($this->repository->list_formatter);
	}

	protected function lazy_get_context_transforms(): ContextTransforms
	{
		try
		{
			return new ContextTransforms($this['contextTransforms']);
		}
		catch (ResourceNotFound $e)
		{
			// Not all locales have context transforms e.g. zh
			return new ContextTransforms([]);
		}
	}

	protected function lazy_get_units(): Units
	{
		return new Units($this);
	}

	/**
	 * Localize the specified source.
	 *
	 * @param object|string $source_or_code
	 *     The source to localize, or the locale code to localize this instance.
	 * @param array<string, mixed> $options
	 *     The options are passed to the localizer.
	 *
	 * @return mixed
	 */
	public function localize($source_or_code, array $options = [])
	{
		if (is_string($source_or_code))
		{
			return $this->repository->locales[$source_or_code]->localize($this, $options);
		}

		$constructor = $this->resolve_localize_constructor($source_or_code);

		if ($constructor)
		{
			return $constructor($source_or_code, $this, $options);
		}

		throw new LogicException("Unable to localize source");
	}

	/**
	 * @param object $source
	 */
	private function resolve_localize_constructor($source): ?callable
	{
		$class = get_class($source);

		if ($source instanceof Localizable)
		{
			return [ $class, 'localize' ]; // @phpstan-ignore-line
		}

		$base = basename(strtr($class, '\\', '/'));
		$constructor = __NAMESPACE__ . "\\Localized$base";

		if (!class_exists($constructor))
		{
			return null;
		}

		return [ $constructor, 'from' ]; // @phpstan-ignore-line
	}

	/**
	 * Formats a number using {@link $number_formatter}.
	 *
	 * @param float|int $number
	 *
	 * @see LocalizedNumberFormatter::format
	 */
	public function format_number($number, ?string $pattern = null): string
	{
		return $this->number_formatter->format($number, $pattern);
	}

	/**
	 * @param float|int $number
	 *
	 * @see LocalizedNumberFormatter::format
	 */
	public function format_percent($number, ?string $pattern = null): string
	{
		return $this->number_formatter->format(
			$number,
			$pattern ?: $this->numbers->percent_formats['standard']
		);
	}

	/**
	 * Formats currency using localized conventions.
	 *
	 * @param float|int $number
	 * @param Currency|string $currency
	 */
	public function format_currency(
		$number,
		$currency,
		string $pattern = LocalizedCurrencyFormatter::PATTERN_STANDARD
	): string {
		return $this->currency_formatter->format($number, $currency, $pattern);
	}

	/**
	 * Formats a variable-length lists of scalars.
	 *
	 * @param scalar[] $list
	 * @param LocalizedListFormatter::TYPE_* $type
	 *
	 * @see LocalizedListFormatter::format()
	 */
	public function format_list(array $list, string $type = LocalizedListFormatter::TYPE_STANDARD): string
	{
		return $this->list_formatter->format($list, $type);
	}

	/**
	 * Transforms a string depending on the context and the locale rules.
	 *
	 * @param string $usage One of `ContextTransforms::USAGE_*`
	 * @param string $type One of `ContextTransforms::TYPE_*`
	 */
	public function context_transform(string $str, string $usage, string $type): string
	{
		return $this->context_transforms->transform($str, $usage, $type);
	}
}
