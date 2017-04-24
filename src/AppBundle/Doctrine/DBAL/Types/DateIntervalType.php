<?php
/**
 * Date: 30-Aug-16
 * Time: 16:36
 */

namespace AppBundle\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\TimeType;

class DateIntervalType extends TimeType
{
    const DATEINTERVAL = 'date_interval';

    /**
     * @override
     * @param \DateInterval $value
     * @param AbstractPlatform $platform
     * @return mixed|null
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value === null ? null : ((int)$value->format('%a') * 24 + (int)$value->format('%H')) . $value->format(':%I:%S');
    }

    /**
     * @override
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return \DateInterval|\DateTime|mixed|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value !== null) {
            list($hours, $minutes, $seconds) = sscanf($value, '%d:%d:%d');
            return new \DateInterval(sprintf('PT%dH%dM%dS', $hours, $minutes, $seconds));
        }
        return null;
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return self::DATEINTERVAL;
    }
}