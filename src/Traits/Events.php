<?php
/*
 | nebulaBB Forum Software
 |
 | Author: Remzi Kocak <kocak0068@gmail.com>
 | Created at: 28.01.20 18:37
 | Version: 1.0.0
 */

namespace Erbilen\Traits;


trait Events
{

	/**
	 * .on() metodunu dönüştürür
	 */
	public static function on()
	{

		// fonksiyon çağırımı için
		$pattern = "@\.on\('([0-9a-zA-Z-_]+)',\s?([0-9a-zA-Z-_]+)\);?@s";
		self::$js = preg_replace_callback($pattern, function ($js) {
			if (isset($js[1]) && !empty($js[1]))
				return '.addEventListener(\'' . $js[1] . '\', ' . $js[2] . ');';
		}, self::$js);

		// inline callback fonksiyonu için
		$pattern = "@\.on\('([0-9a-zA-Z-_]+)',\s?function\s?\(([0-9a-zA-Z-_, ]+|)\)\s?{(.*?)}\);?@s";
		self::$js = preg_replace_callback($pattern, function ($js) {
			if (isset($js[1]) && !empty($js[1]))
				return '.addEventListener(\'' . $js[1] . '\', (' . (!empty($js[2]) ? $js[2] : 'e') . ') => {' . self::this($js[3], $js[2]) . '});';
		}, self::$js);
	}

	/**
	 * .on() metodundaki $(this) değerini değiştirir
	 * @param $js
	 * @return string|string[]|null
	 */
	public static function this($js, $event = null)
	{
		$pattern = "@\\$\(\s?this\s?\)@";
		return preg_replace_callback($pattern, function ($js) use ($event) {
			return (empty($event) ? 'e' : $event) . '.target';
		}, $js);
	}

	/**
	 * .trigger() metodunu dönüştürür
	 */
	public static function trigger()
	{
		$pattern = '@document.(getElementByClassName|getElementById|querySelector|querySelectorAll)\("([0-9a-zA-Z-_]+)"\)\.trigger\((\'|")([a-zA-Z]+)(\'|")\);?@';
		self::$js = preg_replace_callback($pattern, function ($js) {
			return 'var event = document.createEvent(\'HTMLEvents\');
	event.initEvent(\'' . $js[4] . '\', true, false);
	document.' . $js[1] . '("' . $js[2] . '").dispatchEvent(event);
';
		}, self::$js);
	}

}
