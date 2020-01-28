<?php

namespace Erbilen;

use Erbilen\Traits\Selectors;
use Erbilen\Traits\Events;
use Erbilen\Traits\Requests;
use Erbilen\Traits\DOM;

class JqueryToJS
{
	use Selectors, Events, Requests, DOM;

	public static $js;
	public static $replaceVarToLet = true;
	public static $removeComments = true;

	public static function check($param)
	{
		return isset($param) && !empty($param);
	}

	public static function documentReady()
	{
		$pattern = '@\$\(\s?function\s?\(\)\s?\{(.*?)\}\s?\)(;|)@sU';
		self::$js = preg_replace_callback($pattern, function ($js) {
			return 'document.addEventListener("DOMContentLoaded", () => {' . $js[1] . '});';
		}, self::$js);
	}

	/**
	 * Açıklama satırlarını siler
	 */
	public static function removeComments()
	{

		// çok satırlı açıklamaları sil
		$pattern = '@/\*(.*?)\*/@s';
		self::$js = preg_replace($pattern, null, self::$js);

		// tek satırlı açıklamarı sil
		$pattern = '@//(.+)@';
		self::$js = preg_replace($pattern, null, self::$js);
	}

	public static function convert($js)
	{
		self::$js = $js;
		self::documentReady();
		if (self::$removeComments)
			self::removeComments();
		if (self::$replaceVarToLet)
			self::varToLet();
		self::replaceVars();
		self::idSelectors();
		self::classSelectors();
		self::html();
		self::text();
		self::addClass();
		self::removeClass();
		self::toggleClass();
		self::hasClass();
		self::hide();
		self::show();
		self::remove();
		self::val();
		self::next();
		self::prev();
		self::clone();
		self::each();
		self::on();
		self::trigger();
		self::ajax();
		self::getJSON();
		return self::$js;
	}

}
