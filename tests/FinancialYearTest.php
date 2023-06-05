<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Hampel\Calendar\FinancialYear;
use PHPUnit\Framework\TestCase;

class FinancialYearTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_non_numeric_start_throws_exception()
    {
        $this->expectException(\TypeError::class);

        new FinancialYear('foo');
    }

    public function test_non_numeric_end_throws_exception()
    {
        $this->expectException(\TypeError::class);

        new FinancialYear(0, 'foo');
    }

    public function test_start_after_end_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("end year must be the year after start year");

        new FinancialYear(2, 1);
    }

    public function test_start_and_end_not_sequential_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("end year must be the year after start year");

        new FinancialYear(1, 3);
    }

    public function test_constructor()
    {
        $fy = new FinancialYear(2020);
        $this->assertEquals("2020-07-01", $fy->startDate()->toDateString());
        $this->assertEquals("2021-06-30", $fy->endDate()->toDateString());
    }

    public function test_create_with_no_value_creates_current_fy()
    {
        Carbon::setTestNow(Carbon::create(2020, 8, 9));

        $fy = FinancialYear::create();

        $this->assertEquals("2020-07-01", $fy->startDate()->toDateString());
        $this->assertEquals("2021-06-30", $fy->endDate()->toDateString());
    }

    public function test_create_with_value()
    {
        $fy = FinancialYear::create('2020');

        $this->assertEquals("2020-07-01", $fy->startDate()->toDateString());
        $this->assertEquals("2021-06-30", $fy->endDate()->toDateString());
    }

    public function test_create_from_date()
    {
        $fy = FinancialYear::createFromDate(Carbon::create(2021, 2, 3));

        $this->assertEquals("2020-07-01", $fy->startDate()->toDateString());
        $this->assertEquals("2021-06-30", $fy->endDate()->toDateString());

        $fy = FinancialYear::createFromDate(Carbon::create(2020, 8, 9));

        $this->assertEquals("2020-07-01", $fy->startDate()->toDateString());
        $this->assertEquals("2021-06-30", $fy->endDate()->toDateString());
    }

    public function test_create_from_ymd()
    {
        $fy = FinancialYear::createFromYmd("2021-02-03");

        $this->assertEquals("2020-07-01", $fy->startDate()->toDateString());
        $this->assertEquals("2021-06-30", $fy->endDate()->toDateString());

        $fy = FinancialYear::createFromYmd("2020-08-09");

        $this->assertEquals("2020-07-01", $fy->startDate()->toDateString());
        $this->assertEquals("2021-06-30", $fy->endDate()->toDateString());
    }

    public function test_create_from_string()
    {
        $fy = FinancialYear::createFromString("2020-21");

        $this->assertEquals("2020-07-01", $fy->startDate()->toDateString());
        $this->assertEquals("2021-06-30", $fy->endDate()->toDateString());
    }

    public function test_thisFinancialYear()
    {
        Carbon::setTestNow(Carbon::create(2020, 8, 9));

        $fy = FinancialYear::thisFinancialYear();

        $this->assertEquals("2020-07-01", $fy->startDate()->toDateString());
        $this->assertEquals("2021-06-30", $fy->endDate()->toDateString());

        $this->assertTrue($fy->inFinancialYear(Carbon::today()));
    }

    public function test_nextFinancialYear()
    {
        Carbon::setTestNow(Carbon::create(2020, 8, 9));

        $thisFy = FinancialYear::thisFinancialYear();
        $nextFy = FinancialYear::nextFinancialYear();

        $this->assertEquals("2020-21", $thisFy->toString());
        $this->assertEquals("2021-22", $nextFy->toString());
    }

    public function test_next()
    {
        $fy = new FinancialYear(2020);

        $this->assertEquals("2020-07-01", $fy->startDate()->toDateString());
        $this->assertEquals("2021-06-30", $fy->endDate()->toDateString());

        $next = $fy->next();

        $this->assertEquals("2021-07-01", $next->startDate()->toDateString());
        $this->assertEquals("2022-06-30", $next->endDate()->toDateString());
    }

    public function test_inFinancialYear()
    {
        $fy = new FinancialYear(2020);

        $this->assertFalse($fy->inFinancialYear(Carbon::create(2019, 8, 9)));
        $this->assertFalse($fy->inFinancialYear(Carbon::create(2020, 6, 30)));
        $this->assertTrue($fy->inFinancialYear(Carbon::create(2020, 7, 1)));
        $this->assertTrue($fy->inFinancialYear(Carbon::create(2020, 8, 9)));
        $this->assertTrue($fy->inFinancialYear(Carbon::create(2021, 6, 30)));
        $this->assertFalse($fy->inFinancialYear(Carbon::create(2021, 8, 9)));
    }

    public function test_isThisFinancialYear()
    {
        Carbon::setTestNow(Carbon::create(2022, 2, 3));

        $this->assertFalse(FinancialYear::create(2020)->isThisFinancialYear());
        $this->assertTrue(FinancialYear::create(2021)->isThisFinancialYear());
        $this->assertFalse(FinancialYear::create(2022)->isThisFinancialYear());

    }

    public function test_toString()
    {
        $fy = new FinancialYear(2020, 2021);

        $this->assertEquals("2020-21", (new FinancialYear(2020, 2021))->toString());

    }
}
