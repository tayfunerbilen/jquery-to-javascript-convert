# jQuery to Javascript Converter
This PHP class helps to convert your (really) simple jquery codes to vanilla javascript codes.

There is a lot of issues, it's not completed yet, yet you can convert your simple jquery codes to vanilla js instead of use jquery library using this php class.

> Inspired from [youmightnotneedjquery.com](http://youmightnotneedjquery.com)

# Installation

Install with composer

```
composer require tayfunerbilen/jquery-to-javascript-convert dev-master
```

then include `autoload.php` file in to your php file.

```php
require __DIR__ . '/vendor/autoload.php';
```

you're ready to go.

# Usage

convert onload function
```php
$js = <<<JS
$(function(){
	
});
JS;
echo \Erbilen\JqueryToJS::convert($js);

/*
document.addEventListener("DOMContentLoaded", () => {
	
});
*/
```

convert variables

```php
echo Erbilen\JqueryToJS::convert("var test");
// let test
```

convert id selectors
```php
echo Erbilen\JqueryToJS::convert("var test = $('#test')");
// let test = document.getElementById("test")
```

convert class selectors
```php
echo Erbilen\JqueryToJS::convert("var list = $('.list')");
// let list = document.getElementByClassName("list")
```

or

```php
echo Erbilen\JqueryToJS::convert("var list = $('.list li')");
// let list = document.querySelectorAll(".list li")
```

convert `html()` method
```php
echo Erbilen\JqueryToJS::convert("var content = $('#content').html()");
// let content = document.getElementById("content").innerHTML
```

or

```php
echo Erbilen\JqueryToJS::convert("var content = $('#content').html('new content')");
// let content = document.getElementById("content").innerHTML = 'new content'
```

convert `text()` method
```php
echo Erbilen\JqueryToJS::convert("var content = $('#content').text()");
// let content = document.getElementById("content").innerText
```

or

```php
echo Erbilen\JqueryToJS::convert("var content = $('#content').text('new content')");
// let content = document.getElementById("content").innerText = 'new content'
```

convert `val()` method
```php
echo Erbilen\JqueryToJS::convert("var name = $('#name').val()");
// let name = document.getElementById("name").value
```

or

```php
echo Erbilen\JqueryToJS::convert("var name = $('#name').val('Tayfun Erbilen')");
// let name = document.getElementById("name").value = 'Tayfun Erbilen'
```

convert `show()` method

```php
echo Erbilen\JqueryToJS::convert("$('#content').show()");
// document.getElementById("content").style.display = ""
```

convert `hide()` method

```php
echo Erbilen\JqueryToJS::convert("$('#content').hide()");
// document.getElementById("content").style.display = "none"
```

convert `remove()` method

```php
echo Erbilen\JqueryToJS::convert("$('#container').remove()");
/*
let container = document.getElementById("container");
container.parentNode.removeChild(container);
*/
```

convert `addClass()` method

```php
echo Erbilen\JqueryToJS::convert("$('#container').addClass('active')");
// document.getElementById("container").classList.add("active")
```

convert `removeClass()` method

```php
echo Erbilen\JqueryToJS::convert("$('#container').removeClass('active')");
// document.getElementById("container").classList.remove("active")
```

convert `hasClass()` method

```php
echo Erbilen\JqueryToJS::convert("$('#container').hasClass('active')");
// document.getElementById("container").classList.contains("active")
```

convert `toggleClass()` method

```php
echo Erbilen\JqueryToJS::convert("$('#container').toggleClass('active')");
// document.getElementById("container").classList.toggle("active")
```

convert `next()` method

```php
echo Erbilen\JqueryToJS::convert("$('#test').next()");
// document.getElementById("test").nextElementSibling;
```

convert `prev()` method

```php
echo Erbilen\JqueryToJS::convert("$('#test').prev()");
// document.getElementById("test").previousElementSibling;
```

convert `on()` method

```php
$js = <<<JS
$('#button').on('click', function (e) {

})
JS;
echo Erbilen\JqueryToJS::convert($js);
/*
document.getElementById("button").addEventListener('click', (e) => {

});
*/
```

or

```php
$js = <<<JS
function callback(e){
	console.log(e);
}
$('#button').on('click', callback);
JS;
echo Erbilen\JqueryToJS::convert($js);
/*
function callback(e){
	console.log(e);
}
document.getElementById("button").addEventListener('click', callback);
*/
```

convert `trigger()` method

```php
echo Erbilen\JqueryToJS::convert("$('#open-btn').trigger('click')");
/*
var event = document.createEvent('HTMLEvents');
event.initEvent('click', true, false);
document.getElementById("open-btn").dispatchEvent(event);
*/
```

convert `ajax()` method

```php
$js = <<<JS
var data = {
	name: "Tayfun",
	surname: "Erbilen"
};

$.ajax({
	type: 'POST',
	url: 'api/contact.php',
	data: data,
	success: function (responseVar) {
		$('#response').html(responseVar);
	},
	error: function (err) {
		$('#error').html(err);
	}
});
JS;
echo Erbilen\JqueryToJS::convert($js);
/*
let data = {
	name: "Tayfun",
	surname: "Erbilen"
};

let request = new XMLHttpRequest();
    request.open('POST', 'api/contact.php', true);

    request.onload = () => {
        if (this.status >= 200 && this.status < 400) {
            let responseVar = this.response;
            document.getElementById("response").innerHTML = responseVar;
        }
    }

    request.onerror = (err) => {
        document.getElementById("error").innerHTML = err;
    }

    request.send(data);
*/
```

convert `$.getJSON()` method

```php
$js = <<<JS
$.getJSON( "api/get-articles", function(json) {
	console.log(json);
});
JS;
echo Erbilen\JqueryToJS::convert($js)

/*
let request = new XMLHttpRequest();
request.open('GET', 'api/get-articles', true);

request.onload = function() {
  if (this.status >= 200 && this.status < 400) {
    let json = JSON.parse(this.response);
    console.log(json);
  }
};

request.send();
*/
```

also you can send data as well

```php
$js = <<<JS
var data = {
    query: "harry",
    limit: 5
};
$.getJSON( "api/get-articles", data, function(json) {
	console.log(json);
});
JS;
echo Erbilen\JqueryToJS::convert($js)

/*
let data = {
    query: "harry",
    limit: 5
};
let request = new XMLHttpRequest();
request.open('POST', 'api/get-articles', true);

request.onload = function() {
  if (this.status >= 200 && this.status < 400) {
    let json = JSON.parse(this.response);
    console.log(json);
  }
};

request.send(JSON.stringify(data));
*/
```

# converting little bit complex code
```php
$js = <<<JS
$(function () {

    $('#button').on('click', function (e) {
        var container = $('#container'),
            text = $('#text');
        if (container.hasClass('active')) {
            container.removeClass('active');
            text.html('<b>container hidden</b>');
        } else {
            container.addClass('actived');
            text.html('<b>container showed</b>');
        }
        e.preventDefault();
    });

    $('#load-btn').on('click', function (e) {

        $.ajax({
            type: 'GET',
            url: 'api/load-more',
            success: function (result) {
                console.log(result);
            },
            error: function (err) {
                console.log(err);
            }
        });

        e.preventDefault();
    });

});
JS;

echo \Erbilen\JqueryToJS::convert($js);

/*

document.addEventListener("DOMContentLoaded", () => {

    document.getElementById("button").addEventListener('click', (e) => {
        let container = document.getElementById("container"),
            text = document.getElementById("text");
        if (container.classList.contains("active")) {
            container.classList.remove("active");
            text.innerHTML = '<b>container hidden</b>';
        } else {
            container.classList.add("actived");
            text.innerHTML = '<b>container showed</b>';
        }
        e.preventDefault();
    });

    document.getElementById("load-btn").addEventListener('click', (e) => {

        let request = new XMLHttpRequest();
        request.open('GET', 'api/load-more', true);

        request.onload = () => {
            if (this.status >= 200 && this.status < 400) {
                let result = this.response;
                console.log(result);
            }
        };

        request.onerror = (err) => {
            console.log(err);
        };

        request.send();

        e.preventDefault();
    });

});
*/
```
