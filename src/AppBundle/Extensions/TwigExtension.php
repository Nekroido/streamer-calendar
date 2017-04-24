<?php
/**
 * Date: 27-Aug-16
 * Time: 20:37
 */

namespace AppBundle\Extensions;

use AppBundle\Helpers\CalendarHelper;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class TwigExtension extends \Twig_Extension
{
    use ContainerAwareTrait;

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('monthName', array($this, 'getMonthName')),
            new \Twig_SimpleFilter('weekday', array($this, 'getWeekday')),
            new \Twig_SimpleFilter('weekdayShort', array($this, 'getWeekdayShort')),
            new \Twig_SimpleFilter('isWeekend', array($this, 'isWeekend')),
            new \Twig_SimpleFilter('detectBrightness', array($this, 'detectBrightness')),
        );
    }

    public function getMonthName($month)
    {
        return CalendarHelper::getMonthName($month);
    }

    public function getWeekdayShort($weekday)
    {
        return CalendarHelper::getWeekdayShort($weekday);
    }

    public function getWeekday($weekday)
    {
        return CalendarHelper::getWeekday($weekday);
    }

    public function isWeekend($weekday)
    {
        return CalendarHelper::isWeekend($weekday);
    }

    public function detectBrightness($color)
    {
        return CalendarHelper::getColorBrightness($color) > 130 ? 'light' : 'dark';
    }

    public function getName()
    {
        return 'twig_extension';
    }
}