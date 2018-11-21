<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConstTest extends TestCase
{
    /**
     * @test
     */
    public function SIZES__sizesで定義している定数をテストする():void
    {
        $this->assertSame('std', config('const.SIZES')[0]);
        $this->assertSame('medium', config('const.SIZES')[1]);
        $this->assertSame('high', config('const.SIZES')[2]);
    }

//    /**
//     * @test
//     */
//    public function SIZES__sizesで定義している定数をテストする():void
//    {
//        $this->assertSame('std', config('const.SIZES')[0]);
//        $this->assertSame('medium', config('const.SIZES')[1]);
//        $this->assertSame('high', config('const.SIZES')[2]);
//    }
}
