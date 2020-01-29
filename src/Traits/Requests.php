<?php
/*
 | nebulaBB Forum Software
 |
 | Author: Remzi Kocak <kocak0068@gmail.com>
 | Created at: 28.01.20 18:37
 | Version: 1.0.0
 */

namespace Erbilen\Traits;


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
				$pattern = "@type\s?:\s?('|\")(GET|POST)('|\")@i";
				preg_match($pattern, $js[0], $type);
				$type = $type[2] ?? null;

				// dataType değerini al
				$pattern = "@dataType\s?:\s?('|\")(.*?)('|\")@i";
				preg_match($pattern, $js[0], $dataType);
				$dataType = $dataType[2] ?? null;

				// url değerini al
				$pattern = "@url\s?:\s?('|\")([a-zA-Z-_/.]+)('|\")@i";
				preg_match($pattern, $js[0], $url);
				$url = $url[2] ?? null;

				// data değerini al
				$pattern = "@data\s?:\s?('|)([a-zA-Z-_/.& =]+)('|)@i";
				preg_match($pattern, $js[0], $data);
				$data = ($data[1] ?? null) . ($data[2] ?? null) . ($data[3] ?? null);

				// success callback fonksiyonunu al
				$pattern = "@success\s?:\s?function\s?\((.*?)\)\s?\{(.*?)\}@s";
				preg_match($pattern, $js[0], $successFunction);
				$successVariable = $successFunction[1] ?? null;
				$successCallback = trim($successFunction[2]) ?? null;

				// error callback fonksiyonunu al
				$pattern = "@error\s?:\s?function\s?\((.*?)\)\s?\{(.*?)\}@s";
				preg_match($pattern, $js[0], $errorFunction);
				$errorVariable = $errorFunction[1] ?? null;
				$errorCallback = trim($errorFunction[2]) ?? null;

			}

			$js = <<<JS
let request = new XMLHttpRequest();
request.open("{$type}", "{$url}", true);
JS;

			if ($dataType)
				$js .= <<<JS
\nrequest.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
JS;

			if ($successVariable)
				$js .= <<<JS
\nrequest.onload = function() {
	if (this.status >= 200 && this.status < 400) {
		let {$successVariable} = this.response;
		{$successCallback}
	}
}
JS;

			if ($errorVariable)
				$js .= <<<JS
\nrequest.onerror = function({$errorVariable}) {
	{$errorCallback}
};
JS;

			$js .= <<<JS
\nrequest.send({$data});
JS;
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
