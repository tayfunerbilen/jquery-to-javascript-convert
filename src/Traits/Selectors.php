<?php
/*
 | nebulaBB Forum Software
 |
 | Author: Remzi Kocak <kocak0068@gmail.com>
 | Created at: 28.01.20 18:36
 | Version: 1.0.0
 */

namespace Erbilen\Traits;


trait Selectors
{

	/**
	 * ID seçicileri js formatına dönüştürür
	 */
	public static function idSelectors()
	{
		$pattern = "@\\$\(\s?('|\")\s?\#([0-9a-zA-Z-_ ]+)\s?('|\")\s?\)@";
		self::$js = preg_replace_callback($pattern, function ($js) {
			if (isset($js[2]) && !empty($js[2]))
				// eğer boşluk varsa multiple etiket seçeceğini kabul et
				if (strstr(trim($js[2]), ' '))
					return 'document.querySelectorAll("#' . trim($js[2]) . '")';
				else
					return 'document.getElementById("' . trim($js[2]) . '")';
		}, self::$js);
	}

	/**
	 * Class seçicileri js formatına dönüştürür
	 */
	public static function classSelectors()
	{
		$pattern = "@\\$\(\s?('|\")\s?\.([0-9a-zA-Z-_\s]+)\s?('|\")\s?\)@";
		self::$js = preg_replace_callback($pattern, function ($js) {
			if (isset($js[2]) && !empty($js[2]))
				// eğer boşluk varsa multiple etiket seçeceğini kabul et
				if (strstr(trim($js[2]), ' '))
					return 'document.querySelectorAll(".' . trim($js[2]) . '")';
				else
					return 'document.getElementByClassName("' . trim($js[2]) . '")';
		}, self::$js);
	}

}
