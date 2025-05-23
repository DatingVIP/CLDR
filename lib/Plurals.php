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

use ArrayObject;
use ICanBoogie\CLDR\Plurals\Rule;
use ICanBoogie\CLDR\Plurals\Samples;

use function array_keys;
use function array_shift;
use function explode;
use function strlen;
use function strpos;
use function substr;
use function trim;

/**
 * Representation of plurals
 *
 * @extends ArrayObject<string, array<string, string>>
 *     Where _key_ is a locale and _value_ a rule.
 */
final class Plurals extends ArrayObject
{
	public const COUNT_ZERO = 'zero';
	public const COUNT_ONE = 'one';
	public const COUNT_TWO = 'two';
	public const COUNT_FEW = 'few';
	public const COUNT_MANY = 'many';
	public const COUNT_OTHER = 'other';

	public const RULE_COUNT_PREFIX = 'pluralRule-count-';

	/**
	 * @var array<string, array<string, Rule>>
	 *     Where _key_ is a locale code and _value_ an array where _key_ is a rule count.
	 */
	private $rules = [];

	/**
	 * @var array<string, array<string, Samples>>
	 *     Where _key_ is a locale code and _value_ an array where _key_ is a rule count.
	 */
	private $samples = [];

	/**
	 * @param float|int $number
	 *
	 * @return string One of `COUNT_*`.
	 */
	public function rule_for($number, string $locale): string
	{
		foreach ($this->rule_instances_for($locale) as $count => $rule)
		{
			if ($rule->validate($number))
			{
				return $count;
			}
		}

		return self::COUNT_OTHER; // @codeCoverageIgnore
	}

	/**
	 * @return string[]
	 */
	public function rules_for(string $locale): array
	{
		return array_keys($this->rule_instances_for($locale));
	}

	/**
	 * @return array<string, Samples>
	 */
	public function samples_for(string $locale): array
	{
		$samples = &$this->samples[$locale];

		return $samples ?: $samples = $this->create_samples_for($locale);
	}

	/**
	 * @return array<string, Rule>
	 */
	private function rule_instances_for(string $locale): array
	{
		$rules = &$this->rules[$locale];

		return $rules ?: $rules = $this->create_rules_for($locale);
	}

	/**
	 * @return array<string, Rule>
	 */
	private function create_rules_for(string $locale): array
	{
		$rules = [];
		$prefix_length = strlen(self::RULE_COUNT_PREFIX);

		foreach ($this[$locale] as $count => $rule_string)
		{
			$count = substr($count, $prefix_length);
			$rules[$count] = Rule::from($this->extract_rule($rule_string));
		}

		return $rules;
	}

	private function extract_rule(string $rule_string): string
	{
		$rule = explode('@', $rule_string, 2);
		$rule = array_shift($rule);

		return trim($rule);
	}

	/**
	 * @return array<string, Samples>
	 */
	private function create_samples_for(string $locale): array
	{
		$samples = [];
		$prefix_length = strlen(self::RULE_COUNT_PREFIX);

		foreach ($this[$locale] as $count => $rule_string)
		{
			$count = substr($count, $prefix_length);
			$samples[$count] = Samples::from($this->extract_samples($rule_string));
		}

		return $samples;
	}

	private function extract_samples(string $rule_string): string
	{
		$pos = strpos($rule_string, '@');

		if ($pos === false) {
			throw new \Exception("Unable to locate rule string in '$rule_string'");
		}

		return substr($rule_string, $pos);
	}
}
