<?php namespace Hampel\Calendar;

use Carbon\Carbon;
use InvalidArgumentException;

class FinancialYear
{
    protected Carbon $start;

    protected Carbon $end;

    /**
     * @param int $startYear start year, must be a numeric value
     * @param int $endYear (optional) end year - if supplied, must be a numeric value and one higher than start year
     */
    public function __construct($startYear, $endYear = null)
    {
        if (!is_numeric($startYear))
        {
            throw new InvalidArgumentException("Start year must be numeric");
        }

        if ($endYear)
        {
            if (!is_numeric($endYear))
            {
                throw new InvalidArgumentException("End year must be numeric");
            }
            if (($startYear > $endYear) || (($endYear - $startYear) != 1))
            {
                throw new InvalidArgumentException("Start year must be the year before end year");
            }
        }
        else
        {
            $endYear = $startYear + 1;
        }

        $this->start = Carbon::create($startYear, 7, 1);
        $this->end = Carbon::create($endYear, 6, 30);
    }

    /**
     * @param int $startYear (optional) start year - if not specified, will use the current date to determine the financial year
     * @return FinancialYear
     */
    public static function create($startYear = null) : FinancialYear
    {
        if (!$startYear)
        {
            return self::createFromDate(Carbon::today());
        }

        return new self($startYear);
    }

    /**
     * @param Carbon $date a Carbon date object which will be used to determine the financial year
     * @return FinancialYear
     */
    public static function createFromDate(Carbon $date) : FinancialYear
    {
        if ($date->month <= 6)
        {
            return new self($date->copy()->subYear()->year);
        }
        else
        {
            return new self($date->year);
        }
    }

    /**
     * @param string $ymd a string in the format "Y-m-d" which will be used to determine the financial year
     * @return FinancialYear
     */
    public static function createFromYmd($ymd) : FinancialYear
    {
        return self::createFromDate(Carbon::createFromFormat("Y-m-d", $ymd));
    }

    /**
     * @param string $fy a string in the format "0000-00" representing the financial year. The first four digits of the string will be used to create the financial year object
     * @return FinancialYear
     */
    public static function createFromString($fy) : FinancialYear
    {
        return self::create(substr($fy, 0, 4));
    }

    /**
     * Use today's date to create the financial year
     *
     * @return FinancialYear
     */
    public static function thisFinancialYear() : FinancialYear
    {
        return self::createFromDate(Carbon::today());
    }

    /**
     * Use today's date to create the next financial year
     *
     * @return FinancialYear
     */
    public static function nextFinancialYear() : FinancialYear
    {
        return self::thisFinancialYear()->next();
    }

    /**
     * return a Carbon object representing the start date of the financial year
     *
     * @return Carbon
     */
    public function startDate() : Carbon
    {
        return $this->start;
    }

    /**
     * return a Carbon object representing the end date of the financial year
     *
     * @return Carbon
     */
    public function endDate() : Carbon
    {
        return $this->end;
    }

    /**
     * return a new object representing the following financial year - useful for stepping through data sets
     *
     * @return FinancialYear
     */
    public function next() : FinancialYear
    {
        return self::createFromDate($this->start->copy()->addYear());
    }

    /**
     * return true if the supplied Carbon date is in the financial year
     *
     * @param Carbon $date
     * @return bool
     */
    public function inFinancialYear(Carbon $date)
    {
        return $this->start <= $date && $this->end >= $date;
    }

    /**
     * return true if today is in the financial year
     *
     * @return bool
     */
    public function isThisFinancialYear()
    {
        return $this->inFinancialYear(Carbon::today());
    }

    /**
     * return a string in the format "0000-00" representing the financial year
     *
     * @return string
     */
    public function toString() : string
    {
        $start = $this->start->format("Y");
        $end = $this->end->format("y");

        return sprintf("%s-%s", $start, $end);
    }
}
