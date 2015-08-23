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

/**
 * Binds the {@link Provider} interface to the {@link Storage} interface.
 */
trait ProviderStorageBinding
{
	abstract protected function retrieve($key);

	/**
	 * @inheritdoc
	 */
	public function provide($path)
	{
		return $this->retrieve($path);
	}
}
