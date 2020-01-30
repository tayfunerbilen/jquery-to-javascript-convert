<?php

namespace Erbilen\Traits;


trait DOM
{
	/**
	 * var ile oluşturulmuş değişkenleri let'e dönüştürür
	 */
	public static function varToLet()
	{
		self::$js = preg_replace('@\bvar\s+([0-9a-zA-Z-_]?)@', 'let $1', self::$js);
	}

	/**
	 * $(var) değerini dönüştürür
	 */
	public static function replaceVars()
	{
		$pattern = "@\\$\((\w+)\)@si";
		self::$js = preg_replace_callback($pattern, function ($js) {
			return $js[1];
		}, self::$js);
	}

	/**
	 * .html(x) metodunu dönüştürür
	 */
	public static function html()
	{
		$pattern = "@.html\((.*?)\)@";
		self::$js = preg_replace_callback($pattern, function ($js) {
			if (isset($js[1]) && !empty($js[1]))
				return '.innerHTML = ' . $js[1];
			elseif (isset($js[0]) && !empty($js[0]))
				return '.innerHTML';
		}, self::$js);
	}

	/**
	 * .text(x) metodunu dönüştürür
	 */
	public static function text()
	{
		$pattern = "@.text\((.*?)\)@";
		self::$js = preg_replace_callback($pattern, function ($js) {
			if (isset($js[1]) && !empty($js[1]))
				return '.innerText = ' . $js[1];
			elseif (isset($js[0]) && !empty($js[0]))
				return '.innerText';
		}, self::$js);
	}

	/**
	 * .toggleClass(x) metodunu dönüştürür
	 */
	public static function toggleClass()
	{
		$pattern = "@.toggleClass\(('|\")([0-9a-zA-Z-_]+)('|\")\)@";
		self::$js = preg_replace_callback($pattern, function ($js) {
			return '.classList.toggle("' . $js[2] . '")';
		}, self::$js);
	}

	/**
	 * .addClass(x) metodunu dönüştürür
	 */
	public static function addClass()
	{
		$pattern = "@.addClass\(('|\")([0-9a-zA-Z-_]+)('|\")\)@";
		self::$js = preg_replace_callback($pattern, function ($js) {
			return '.classList.add("' . $js[2] . '")';
		}, self::$js);
	}

	/**
	 * .removeClass(x) metodunu dönüştürür
	 */
	public static function removeClass()
	{
		$pattern = "@.removeClass\(('|\")([0-9a-zA-Z-_]+)('|\")\)@";
		self::$js = preg_replace_callback($pattern, function ($js) {
			return '.classList.remove("' . $js[2] . '")';
		}, self::$js);
	}

	/**
	 * .hasClass(x) metodunu dönüştürür
	 */
	public static function hasClass()
	{
		$pattern = "@.hasClass\(('|\")([0-9a-zA-Z-_]+)('|\")\)@";
		self::$js = preg_replace_callback($pattern, function ($js) {
			return '.classList.contains("' . $js[2] . '")';
		}, self::$js);
	}

	/**
	 * .hide(x) metodunu dönüştürür
	 */
	public static function hide()
	{
		$pattern = "@\.hide\(([0-9]+|)\)@";
		self::$js = preg_replace_callback($pattern, function ($js) {
			return '.style.display = "none"';
		}, self::$js);
	}

	/**
	 * .show(x) metodunu dönüştürür
	 */
	public static function show()
	{
		$pattern = "@\.show\(([0-9]+|)\)@";
		self::$js = preg_replace_callback($pattern, function ($js) {
			return '.style.display = ""';
		}, self::$js);
	}

	/**
	 * .remove(x) metodunu dönüştürür
	 */
	public static function remove()
	{
		$pattern = "@document\.(getElementByClassName|getElementById|querySelector|querySelectorAll)\(('|\")([0-9a-zA-Z-_.\s]+)('|\")\)\.remove\(\);?@";
		self::$js = preg_replace_callback($pattern, function ($js) {
			$varName = str_replace(['.', ' ', '>'], null, $js[3]);
			$el = (self::$replaceVarToLet ? 'let' : 'var') . ' ' . $varName . ' = document.' . $js[1] . '(' . $js[2] . $js[3] . $js[4] . ');';
			return $el . PHP_EOL . "\t" . $varName . ".parentNode.removeChild(" . $varName . ");";
		}, self::$js);
	}

	/**
	 * .val(x) metodunu dönüştürür
	 */
	public static function val()
	{
		$pattern = "@\.val\(([0-9a-zA-Z-_'\"]+|)\)@";
		self::$js = preg_replace_callback($pattern, function ($js) {
			if (!empty($js[1]))
				return '.value = ' . $js[1];
			else
				return '.value';
		}, self::$js);
	}

	/**
	 * .next() metodunu dönüştürür
	 */
	public static function next()
	{
		$pattern = "@\.next\(\)@";
		self::$js = preg_replace_callback($pattern, function ($js) {
			return '.nextElementSibling';
		}, self::$js);
		// @TODO çoklu seçim için güncelleme yapılacak
	}

	/**
	 * .prev() metodunu dönüştürür
	 */
	public static function prev()
	{
		$pattern = "@\.prev\(\)@";
		self::$js = preg_replace_callback($pattern, function ($js) {
			return '.previousElementSibling';
		}, self::$js);
		// @TODO çoklu seçim için güncelleme yapılacak
	}

	/**
	 * .clone() metodunu dönüştürür
	 */
	public static function clone()
	{
		$pattern = "@.clone\(\)@";
		self::$js = preg_replace($pattern, '.cloneNode(true)', self::$js);
	}

	/**
	 * $.each() metodunu dönüştürür
	 */
	public static function each()
	{

		$pattern = "@\\$\.each\(\s?(\w+)\s?\,\s?function\((.*?)\){(.*?)}\s?\)@s";
		self::$js = preg_replace_callback($pattern, function ($js) {
			$params = array_reverse(explode(',', str_replace(' ', null, trim($js[2]))));
			return $js[1] . '.forEach(function(' . implode(', ', $params) . '){
	' . trim($js[3]) . '
})';
		}, self::$js);
	}
}
