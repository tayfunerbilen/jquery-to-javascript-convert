<?php

namespace Erbilen\JqueryToJS\Test;

use PHPUnit\Framework\TestCase;
use \Erbilen\JqueryToJS;

class JqueryToJsTest extends TestCase
{
    public function testIdSelectors()
    {
        $result = JqueryToJS::convert("$('#selector')");
        $this->assertSame($result, "document.getElementById(\"selector\")");
    }

    public function testClassSelectors()
    {
        $result = JqueryToJS::convert("$('.selector')");
        $this->assertSame($result, "document.getElementByClassName(\"selector\")");
    }
}
