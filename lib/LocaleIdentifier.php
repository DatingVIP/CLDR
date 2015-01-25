<?php

namespace ICanBoogie\CLDR;

class LocaleIdentifier
{
	static public function from($locale_identifier)
	{
		list($unicode_language_id, $transformed_extensions, $unicode_locale_extensions) = static::parse_locale_identifier($locale_identifier);
	}

	static public function parse_locale_identifier($locale_identifier)
	{
		$transformed_extensions = null;
		$transformed_extensions_pos = strpos($locale_identifier, "_u_");

		$unicode_locale_extensions = null;
		$unicode_locale_extensions_pos = strpos($locale_identifier, "_t_");

		if ($unicode_locale_extensions_pos)
		{
			$unicode_locale_extensions = substr($locale_identifier, $unicode_locale_extensions_pos + 3);
		}

		if ($transformed_extensions_pos)
		{
			if ($unicode_locale_extensions_pos)
			{
				$transformed_extensions = substr($locale_identifier, $transformed_extensions_pos + 3)
			}
			else
			{
				$transformed_extensions = substr($locale_identifier, $transformed_extensions_pos + 3);
			}
		}
	}

	protected function __construct()
	{

	}
}
