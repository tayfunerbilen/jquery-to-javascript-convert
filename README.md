# jQuery to Javascript Converter
This PHP class helps to convert your (really) simple jquery codes to vanilla javascript codes.

There is a lot of issues, it's not completed yet, yet you can convert your simple jquery codes to vanilla js instead of use jquery library using this php class.

Here some simple examples;

### Examples

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
