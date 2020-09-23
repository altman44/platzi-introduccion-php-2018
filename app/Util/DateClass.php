<?php

namespace App\Util;

class DateClass
{
    private static $monthsNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    private $day;
    private $month;
    private $year;

    public function __construct($day, $month, $year)
    {
        $this->setDay($day);
        $this->setMonth($month);
        $this->setYear($year);
    }

    private function setDay($day)
    {
        if ($day >= 0) {
            $this->day = $day;
        }
    }

    private function setMonth($month)
    {
        if ($month >= 0) {
            $this->month = $month;
        }
    }

    private function setYear($year)
    {
        if ($year >= 0) {
            $this->year = $year;
        }
    }

    public static function getPartsOfDate($date)
    {
        $partsOfDate = explode('-', $date);
        if (count($partsOfDate) == 3) {
            try {
                for ($i = count($partsOfDate) - 1; $i >= 0; $i--) {
                    $partsOfDate[$i] = intval($partsOfDate[$i]);
                }
            } catch (\Exception $e) {
                $partsOfDate = [];
            }
        } else {
            $partsOfDate = [];
        }
        return $partsOfDate;
    }

    public static function getDuration($firstDate, $secondDate = '')
    {
        if (!$secondDate) {
            $secondDate = new \DateTime('now');
        }
        $dateTimeFirstDate = new \DateTime($firstDate);
        $diffBetweenDates = $dateTimeFirstDate->diff($secondDate);
        return $diffBetweenDates;
    }

    public static function getDurationAsString($dateTime, $concatYears = true, $concatMonths = false, $concatDays = false)
    {
        $string = '';
        if ($concatYears) {
            $string .= strlen($string) > 0 ? ' ' : '';
            $string .= self::concatByAmountOfField($dateTime->y, [
                'plural' => ' years',
                'singular' => ' year'
            ]);
        }
        if ($concatMonths) {
            $string .= strlen($string) > 0 ? ' ' : '';
            $string .= self::concatByAmountOfField($dateTime->m, [
                'plural' => ' months',
                'singular' => ' month'
            ]);
        }
        if ($concatDays == 1) {
            $string .= strlen($string) > 0 ? ' ' : '';
            $string .= self::concatByAmountOfField($dateTime->d, [
                'plural' => ' days',
                'singular' => ' day'
            ]);
        }
        return $string;
    }

    private static function concatByAmountOfField($field, $stringAtTheEnd, $showZeros=false)
    {
        $string = '';
        if ($field > 1) {
            $string .= $field . $stringAtTheEnd['plural'];
        } elseif ($field == 1) {
            $string .= $field . $stringAtTheEnd['singular'];
        } elseif ($showZeros & $field == 0) {
            $string .= $field . $stringAtTheEnd['plural'];
        }
        return $string;
    }

    public function getDateAsString()
    {
        $string = '';
        if ($this->day > 0) {
            $string .= $this->day . '/';
            $string .= $this->month . '/';
            $string .= $this->year;
        } else {
            $string .= self::getMonthByNumber($this->month) . ' of ' . $this->year;
        }

        return $string;
    }

    public static function getMonthByNumber($nmb)
    {
        return self::$monthsNames[$nmb - 1];
    }
}
