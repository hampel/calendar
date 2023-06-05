<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Hampel\Calendar\Quarter;
use PHPUnit\Framework\TestCase;

class QuarterTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_non_numeric_year_throws_exception()
    {
        $this->expectException(\TypeError::class);

        new Quarter('foo');
    }

    public function test_non_numeric_quarter_throws_exception()
    {
        $this->expectException(\TypeError::class);

        new Quarter(0, 'foo');
    }

    public function test_quarter_out_of_bounds_throws_exception()
    {
        $this->expectException(\OutOfRangeException::class);
        $this->expectExceptionMessage("quarter must be in the range 1..4");

        new Quarter(2, 5);
    }

    public function test_constructor()
    {
        $q = new Quarter(2020, 2);
        $this->assertEquals("2020-04-01", $q->startDate()->toDateString());
        $this->assertEquals("2020-06-30", $q->endDate()->toDateString());
    }

    // TODO: more tests

}
