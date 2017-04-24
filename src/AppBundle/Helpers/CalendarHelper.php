<?php
/**
 * Date: 27-Aug-16
 * Time: 20:17
 */

namespace AppBundle\Helpers;

class CalendarHelper
{
    const WEEK_START = 'MO';
    const WEEKDAYS = ['MO', 'TU', 'WE', 'TH', 'FR'];

    /**
     * @param array $matrixArray
     * @return array
     */
    public static function addMatrixRange($matrixArray)
    {
        return call_user_func_array(['CalendarHelper', 'addMatrix'], $matrixArray);
    }

    /**
     * @param array $matrix1
     * @param array $matrix2
     * @return array
     */
    public static function addMatrix($matrix1, $matrix2)
    {
        $pos = count($matrix1);
        if ($matrix1[count($matrix1) - 1][count($matrix1[count($matrix1) - 1]) - 1] == $matrix2[0][count($matrix2[0]) - 1]) {
            $pos--;
        }

        $mergedMatrix = array_slice($matrix1, 0, $pos);
        $mergedMatrix = array_merge($mergedMatrix, $matrix2);

        if (func_num_args() > 2) {
            $args = array_slice(func_get_args(), 2);
            array_unshift($args, $mergedMatrix);

            $mergedMatrix = call_user_func_array(['CalendarHelper', 'addMatrix'], $args);
        }

        return $mergedMatrix;
    }

    /**
     * @param int $month
     * @param int $year
     * @return array
     */
    public static function buildMonthMatrix($month, $year)
    {
        if (self::WEEK_START == 'MO') {
            $startWeekdayFormat = 'N';
            $startWeekday = 1;
            $endWeekday = 7;
        } else {
            $startWeekdayFormat = 'w';
            $startWeekday = 0;
            $endWeekday = 6;
        }

        $firstDay = date_create(sprintf('%d-%d-%d', $year, $month, 1));

        $nextMonth = clone($firstDay);
        $nextMonth->add(new \DateInterval('P1M'));

        $lastDay = clone($nextMonth);
        $lastDay->sub(new \DateInterval('P1D'));

        $firstDayWeekday = $firstDay->format($startWeekdayFormat);
        $lastDayWeekday = $lastDay->format($startWeekdayFormat);

        $matrix = [];

        if ($firstDayWeekday > $startWeekday) { // Add days from previous month
            $currentDay = clone($firstDay);
            $currentDay->sub(new \DateInterval('P1D'));
            $matrix[0][] = clone($currentDay);
            while ($currentDay->format($startWeekdayFormat) > $startWeekday) {
                $currentDay->sub(new \DateInterval('P1D'));
                $matrix[0][] = clone($currentDay);
            }
            $matrix[0] = array_reverse($matrix[0]);
        }

        $week = 0;
        $currentDay = clone($firstDay);
        for ($day = 1; $day <= $lastDay->format('d'); $day++) {
            if (@count($matrix[$week]) == 7)
                $week++;

            $matrix[$week][] = clone($currentDay);
            $currentDay->add(new \DateInterval('P1D'));
        }

        if ($lastDayWeekday < $endWeekday) { // Add days from next month
            $matrix[count($matrix) - 1][] = clone($currentDay);
            while ($currentDay->format($startWeekdayFormat) < $endWeekday) {
                $currentDay->add(new \DateInterval('P1D'));
                $matrix[count($matrix) - 1][] = clone($currentDay);
            }
        }

        return $matrix;
    }

    /**
     * @param int $month
     * @param int $year
     * @return array
     */
    public static function buildVerticalMonthMatrix($month, $year)
    {
        return self::rotateMatrix(self::buildMonthMatrix($month, $year));
    }

    /**
     * @param array $simpleMatrix
     * @return array
     */
    public static function rotateMatrix($simpleMatrix)
    {
        $verticalMatrix = [];

        for ($i = 0; $i < count($simpleMatrix[0]); $i++) {
            $verticalMatrix[] = array_column($simpleMatrix, $i);
        }

        return $verticalMatrix;
    }

    /**
     * Prints out formatted calendar days
     *
     * @param array $matrix
     */
    public static function printMatrixFriendly($matrix)
    {
        foreach ($matrix as $row => $columns) {
            foreach ($columns as $date) {
                printf("%d\t", $date->format('d'));
            }
            print "\n\r";
        }
    }

    /**
     * @return array
     */
    public static function getMonthList()
    {
        return [
            1 => 'Январь',
            2 => 'Февраль',
            3 => 'Март',
            4 => 'Апрель',
            5 => 'Май',
            6 => 'Июнь',
            7 => 'Июль',
            8 => 'Август',
            9 => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь',
        ];
    }

    /**
     * @return array
     */
    public static function getWeekdayList()
    {
        return [
            'MO' => 'Понедельник',
            'TU' => 'Вторник',
            'WE' => 'Среда',
            'TH' => 'Четверг',
            'FR' => 'Пятница',
            'SA' => 'Суббота',
            'SU' => 'Воскресенье',
        ];
    }

    /**
     * @return array
     */
    public static function getWeekdayShortList()
    {
        return [
            'MO' => 'Пн',
            'TU' => 'Вт',
            'WE' => 'Ср',
            'TH' => 'Чт',
            'FR' => 'Пт',
            'SA' => 'Сб',
            'SU' => 'Вс',
        ];
    }

    /**
     * @return array
     */
    public static function getDayPositionList()
    {
        return [
            '1' => 'Первый',
            '2' => 'Второй',
            '3' => 'Третий',
            '4' => 'Четвёртый',
            '-1' => 'Последний',
        ];
    }

    /**
     * @return array
     */
    public static function getDayList()
    {
        return array_combine(range(1, 31, 1), range(1, 31, 1));
    }

    /**
     * @param int $number
     * @return string
     */
    public static function getMonthName($number)
    {
        return @self::getMonthList()[(int)$number];
    }

    /**
     * @param string $weekday
     * @return string
     */
    public static function getWeekday($weekday)
    {
        if (strlen($weekday) > 2) {
            $weekday = substr($weekday, 0, 2);
        }

        return (string)self::getWeekdayList()[strtoupper($weekday)];
    }

    /**
     * @param string $weekday
     * @return string
     */
    public static function getWeekdayShort($weekday)
    {
        if (strlen($weekday) > 2) {
            $weekday = substr($weekday, 0, 2);
        }

        return self::getWeekdayShortList()[strtoupper($weekday)];
    }

    /**
     * @param string $position
     * @return string
     */
    public static function getDayPosition($position)
    {
        return @self::getDayPositionList()[$position];
    }

    /**
     * @param string $weekday
     * @return bool
     */
    public static function isWeekend($weekday)
    {
        if (strlen($weekday) > 2) {
            $weekday = substr($weekday, 0, 2);
        }

        return !in_array(strtoupper($weekday), self::WEEKDAYS);
    }

    /**
     * Calculates and returns color brightness from 0 to 255
     *
     * @param string $hex
     * @return float
     */
    public static function getColorBrightness($hex)
    {
        $hex = str_replace('#', '', $hex);

        $c_r = hexdec(substr($hex, 0, 2));
        $c_g = hexdec(substr($hex, 2, 2));
        $c_b = hexdec(substr($hex, 4, 2));

        return (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
    }
}