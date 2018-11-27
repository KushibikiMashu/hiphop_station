<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class ApiControllerTest extends TestCase
{
    private $instance;

    public function setUp()
    {
        parent::setUp();
        $this->instance = new \App\Http\Controllers\ApiController;
    }

    public function testExample()
    {
        $this->assertTrue(true);
    }
}
