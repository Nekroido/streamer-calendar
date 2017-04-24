<?php
/**
 * Date: 30-Aug-16
 * Time: 19:02
 */

namespace AppBundle\Services;

use AppBundle\Entity\StreamEntry;
use IntervalTree\DateRangeExclusive;
use IntervalTree\DateRangeInclusive;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class UiService
{
    protected $es;

    private $hourHeight = 42;

    private $conflicts = [];

    /**
     * @InjectParams({
     *    "em" = @Inject("app.event_service")
     * })
     * @param EventService $es
     */
    public function __construct(EventService $es)
    {
        $this->es = $es;
    }

    /**
     * @param StreamEntry $entry
     * @return float
     */
    public function durationToHeight($entry)
    {
        $duration = $entry->getDuration();
        if($entry->getStart()->format('Ymd') != $entry->getEnd()->format('Ymd')) {
            $dayEnd = date_create($entry->getStart()->format('Y-m-d 00:00:00'));
            $dayEnd->add(new \DateInterval('P1D'));
            $duration = $entry->getStart()->diff($dayEnd);
        }

        return $this->es->durationToSeconds($duration) / 3600 * $this->hourHeight - 1; // Compensating a border
    }

    public function timeStartToOffset($timeStart)
    {
        return round($this->hourHeight / 60 * $timeStart->format('i'));
    }

    /**
     * @param $tree
     * @param StreamEntry $entry
     * @return float
     */
    public function treeToWidth($tree, StreamEntry $entry)
    {
        $count = count($tree->search(new DateRangeInclusive(
            $entry->getStart(), $entry->getEnd(), $entry->getDuration()
        )));
        return 100 / ($count > 0 ? $count :1);
    }

    /**
     * @param $intersection
     * @return float
     */
    public function intersectionToWidth($intersection)
    {
        $count = count($intersection);
        return 100 / ($count > 0 ? $count :1);
    }

    /**
     * @param $tree
     * @param StreamEntry $entry
     * @return mixed
     */
    public function treeToOffsetLeft($tree, StreamEntry $entry, $index)
    {
        $intersection = $tree->search(new DateRangeInclusive(
            $entry->getStart(), $entry->getEnd(), $entry->getDuration()
        ));

        $exclusion = new DateRangeExclusive(
            $entry->getStart(), $entry->getEnd(), $entry->getDuration()
        );

        //$calculatedPos = array_search($exclusion, $intersection);
        $pos = 0;
        $time = $exclusion->getStart()->format('Gi');

        if (isset($this->conflicts[$time])) {
            $diff = array_diff($this->conflicts[$time], [$index]);
            $pos = count($diff);
        }

        $this->conflicts[$time][] = $index;

        return $pos * $this->intersectionToWidth($intersection);
    }
}