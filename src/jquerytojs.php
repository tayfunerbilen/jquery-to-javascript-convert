<?php

namespace Erbilen;

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

trait Requests
{

	/**
	 * .ajax() metodunu dönüştürür
	 */
	public static function ajax()
	{
		$pattern = "@\\$\.ajax\(\{(.*?)\}\);?@s";
		self::$js = preg_replace_callback($pattern, function ($js) {
			if (self::check($js[0])) {

				// type değerini al
				$pattern = "@type\s?:\s?'(GET|POST)'@i";
				preg_match($pattern, $js[0], $type);
				$type = $type[1] ?? null;

				// url değerini al
				$pattern = "@url\s?:\s?'([a-zA-Z-_/.]+)'@i";
				preg_match($pattern, $js[0], $url);
				$url = $url[1] ?? null;

				// data değerini al
				$pattern = "@data\s?:\s?('|)([a-zA-Z-_/.& =]+)('|)@i";
				preg_match($pattern, $js[0], $data);
				$data = ($data[1] ?? null) . ($data[2] ?? null) . ($data[3] ?? null);

				// success callback fonksiyonunu al
				$pattern = "@success\s?:\s?function\s?\((.*?)\)\s?\{(.*?)\}@s";
				preg_match($pattern, $js[0], $successFunction);
				$successVariable = $successFunction[1] ?? 'response';
				$successCallback = $successFunction[2] ?? null;

				// error callback fonksiyonunu al
				$pattern = "@error\s?:\s?function\s?\((.*?)\)\s?\{(.*?)\}@s";
				preg_match($pattern, $js[0], $errorFunction);
				$errorVariable = $errorFunction[1] ?? null;
				$errorCallback = $errorFunction[2] ?? null;

			}

			$js = 'let request = new XMLHttpRequest();
    request.open(\'' . $type . '\', \'' . $url . '\', true);

    request.onload = () => {
        if (this.status >= 200 && this.status < 400) {
            let ' . $successVariable . ' = this.response;
            ' . trim($successCallback) . '
        }
    }

    request.onerror = (' . $errorVariable . ') => {
        ' . trim($errorCallback) . '
    }

    request.send(' . $data . ');';
			return $js;
		}, self::$js);
	}

	/**
	 * $.getJSON() metodunu dönüştürür
	 */
	public static function getJSON()
	{
		$pattern = "@\\$\.getJSON\(\s?('|\")(.*?)('|\")\s?,\s?({|)(.*?|[0-9a-zA-Z]+)(}|)(,|)\s?function\(\s?(.*?)\s?\)\s?{(.*?)}\);?@s";
		self::$js = preg_replace_callback($pattern, function ($js) {

			return 'let request = new XMLHttpRequest();
request.open(\'' . ($js[5] ? 'POST' : 'GET') . '\', \'' . $js[2] . '\', true);

request.onload = function() {
  if (this.status >= 200 && this.status < 400) {
    let ' . $js[8] . ' = JSON.parse(this.response);
    ' . trim($js[9]) . '
  }
};

request.send(' . ($js[5] ? 'JSON.stringify(' : null) . $js[4] . $js[5] . $js[6] . ($js[5] ? ')' : null) . ');';
		}, self::$js);
	}

}

trait DOM
{
	/**
	 * var ile oluşturulmuş değişkenleri let'e dönüştürür
	 */
	public static function varToLet()
	{
		self::$js = str_replace('var', 'let', self::$js);
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
			$el = 'let ' . $varName . ' = document.' . $js[1] . '(' . $js[2] . $js[3] . $js[4] . ');';
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
}

class JqueryToJS
{

	use Selectors, Events, Requests, DOM;

	public static $js;
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
		self::varToLet();
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
		self::on();
		self::trigger();
		self::ajax();
		self::getJSON();
		return self::$js;
	}

}
