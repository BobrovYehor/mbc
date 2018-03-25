<?php

namespace App\Services;

class DayService
{
    const SUNDAY = 0;
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;

    const WEEKEND_MESSAGE = "Its a weekend";
    const WORKDAY_MESSAGE = "Its a workday";

    protected $holidays = [];

    /**
     * DayService constructor.
     */
    public function __construct()
    {
        $this->holidays = array_map(function ($element) {
            return (object)$element;
        }, config('holidays'));
    }

    /**
     * @param $date
     * @return string
     */
    public function checkDate($date)
    {
        $dateInfo = (object)getdate($date);
        $dateInfo->mweek = $this->getWeekOfMonth($date);

        //check if this day is holiday
        foreach ($this->holidays as $holiday) {

            if (($holiday->mday == $dateInfo->mday &&
                    $holiday->mon == $dateInfo->mon) ||
                ($holiday->wday == $dateInfo->wday &&
                    $holiday->mweek == $dateInfo->mweek &&
                    $holiday->mon == $dateInfo->mon)
            ) {
                return $holiday->desc;
            }
        }
        //check if this day is weekend
        if (in_array($dateInfo->wday, [self::SUNDAY, self::SATURDAY])) {

            return self::WEEKEND_MESSAGE;
        }
        //check if this day is monday after holiday
        if ($dateInfo->wday == self::MONDAY) {
            $prevWeekend = [
                self::SATURDAY => strtotime("-2 days", $date),
                self::SUNDAY => strtotime('-1 days', $date)
            ];

            foreach ($prevWeekend as $date) {

                $dateInfo = (object)getdate($date);

                foreach ($this->holidays as $holiday) {

                    if (($holiday->mday == $dateInfo->mday &&
                        $holiday->mon == $dateInfo->mon)) {
                        return self::WEEKEND_MESSAGE;
                    }
                }
            }
        }

        return self::WORKDAY_MESSAGE;
    }

    /**
     * @param $date
     * @return int
     */
    public function getWeekOfMonth($date)
    {
        return $this->isLastWeek($date) ? -1 : (int)ceil(date('d', $date) / 7);
    }

    /**
     * @param $date
     * @return bool
     */
    public function isLastWeek($date)
    {
        $newDate = strtotime("+7 days", $date);
        return date('m', $newDate) !== date('m', $date) ? true : false;

    }

    /**
     * @param $date
     * @return bool
     */
    public function isWeekend($date)
    {
        $dateOfWeek = strftime("%w", $date);
        return $dateOfWeek == self::SATURDAY || $dateOfWeek == self::SUNDAY ? true : false;
    }

}