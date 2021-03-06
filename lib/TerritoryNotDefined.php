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
 * Exception thrown when a territory is not defined.
 *
 * @property-read string $territory_code The ISO code of the territory.
 */
class TerritoryNotDefined extends \InvalidArgumentException implements Exception
{
    use AccessorTrait;

    /**
     * @var string
     */
    private $territory_code;

    /**
     * @return string
     */
    protected function get_territory_code()
    {
        return $this->territory_code;
    }

    /**
     * @param string $territory_code
     * @param null $message
     * @param \Exception|null $previous
     */
    public function __construct($territory_code, $message = null, \Exception $previous = null)
    {
        $this->territory_code = $territory_code;

        if (!$message)
        {
            $message = "Territory not defined for code: $territory_code.";
        }

        parent::__construct($message, 0, $previous);
    }
}
