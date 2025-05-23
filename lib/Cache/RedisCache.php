<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\CLDR\Cache;

use ICanBoogie\CLDR\Cache;
use Redis;
use RedisCluster;

use function serialize;
use function unserialize;

/**
 * Provides CLDR data from a Redis client.
 */
final class RedisCache implements Cache
{
	public const DEFAULT_PREFIX = 'icanboogie-cldr-';

	/**
	 * @var Redis|RedisCluster
	 */
	private $redis;

	/**
	 * @var string
	 */
	private $prefix;

	/**
	 * @param Redis|RedisCluster $redis
	 */
	public function __construct($redis, string $prefix = self::DEFAULT_PREFIX)
	{
		$this->redis = $redis;
		$this->prefix = $prefix;
	}

	public function get(string $path): ?array
	{
		$data = $this->redis->get($this->prefix . $path);

		if (!$data) {
			return null;
		}

		return unserialize($data); // @phpstan-ignore-line
	}

	public function set(string $path, array $data): void
	{
		$this->redis->set($this->prefix . $path, serialize($data));
	}
}
