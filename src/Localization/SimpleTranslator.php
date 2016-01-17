<?php

/**
 * @copyright   Copyright (c) 2015 ublaboo <ublaboo@paveljanda.com>
 * @author      Pavel Janda <me@paveljanda.com>
 * @package     Ublaboo
 */

namespace Ublaboo\DataGrid\Localization;

use Nette;

class SimpleTranslator extends Nette\Object implements Nette\Localization\ITranslator
{

	/**
	 * @var array
	 */
	private $dictionary;


	public function __construct($dictionary = NULL)
	{
		if (is_array($dictionary)) {
			$this->dictionary = $dictionary;
		}
	}


	/**
	 * Translates the given string
	 * 
	 * @param  string
	 * @param  int
	 * @param  array $args
	 * @return string
	 */
	public function translate($message, $count = NULL)
	{
		return isset($this->dictionary[$message]) ? $this->dictionary[$message] : $message;
	}


	public function setDictionary(array $dictionary)
	{
		$this->dictionary = $dictionary;
	}

}
