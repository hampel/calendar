<?php namespace Hampel\Calendar;

use Carbon\Carbon;
use InvalidArgumentException;

class Quarter
{
    protected int $year;

    protected int $quarter;

    protected string $format = '%dQ%d';

    /**
     * @param int $year year, must be a numeric value
     * @param int $quarter quarter, must be a numeric value between 1 and 4
     */
    public function __construct(int $year, int $quarter)
    {
        if ($quarter < 1 || $quarter > 4)
        {
            throw new \OutOfRangeException("quarter must be in the range 1..4");
        }

        $this->year = $year;
        $this->quarter = $quarter;
    }

    /**
     * @param int $year year
     * @param int $quarter quarter (1-4)
     * @return Quarter
     */
    public static function create(int $year, int $quarter) : Quarter
    {
        return new self($year, $quarter);
    }

    /**
     * @param Carbon $date a Carbon date object which will be used to determine the quarter year
     * @return Quarter
     */
    public static function createFromDate(Carbon $date) : Quarter
    {
        return new self($date->year, $date->quarter);
    }

    /**
     * @param string $ymd a string in the format "Y-m-d" which will be used to determine the quarter
     * @return Quarter
     */
    public static function createFromYmd(string $ymd) : Quarter
    {
        return self::createFromDate(Carbon::createFromFormat("Y-m-d", $ymd));
    }

    /**
     * @param string $yq a string in the format "0000Q0" (four digit year, the literal Q, and the 1 digit quarter)
     *                   which will be used to determine the quarter
     * @return Quarter
     */
    public static function createFromString(string $yq) : Quarter
    {
        $year = substr($yq, 0, 4);
        $quarter = substr($yq, 5, 1);

        return self::create($year, $quarter);
    }

    /**
     * Use today's date to create the quarter
     *
     * @return Quarter
     */
    public static function thisQuarter() : Quarter
    {
        return self::createFromDate(Carbon::today());
    }

    /**
     * Use today's date to create the next Quarter
     *
     * @return Quarter
     */
    public static function nextQuarter() : Quarter
    {
        return self::thisQuarter()->next();
    }

    /**
     * return a Carbon object representing the start date of the financial year
     *
     * @return Carbon
     */
    public function startDate() : Carbon
    {
        return Carbon::create($this->year, $this->quarter * 3)->startOfQuarter();
    }

    /**
     * return a Carbon object representing the end date of the financial year
     *
     * @return Carbon
     */
    public function endDate() : Carbon
    {
        return Carbon::create($this->year, $this->quarter * 3)->endOfQuarter();
    }

    /**
     * return a new object representing the following quarter - useful for stepping through data sets
     *
     * @return Quarter
     */
    public function next() : Quarter
    {
        return self::createFromDate($this->startDate()->copy()->addQuarter());
    }

    /**
     * return true if the supplied Carbon date is in the quarter
     *
     * @param Carbon $date
     * @return bool
     */
    public function inQuarter(Carbon $date)
    {
        return $this->startDate()->isSameQuarter($date);
    }

    /**
     * return true if today is in the quarter
     *
     * @return bool
     */
    public function isThisQuarter()
    {
        return $this->inQuarter(Carbon::today());
    }

    /**
     * return a string in the format "0000Q0" representing the quarter
     *
     * @return string
     */
    public function toString() : string
    {
        return sprintf($this->format, $this->year, $this->quarter);
    }
}
