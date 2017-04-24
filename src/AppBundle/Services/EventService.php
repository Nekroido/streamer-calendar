<?php
/**
 * Date: 27-Aug-16
 * Time: 22:03
 */

namespace AppBundle\Services;

use AppBundle\Entity\StreamEntry;
use Doctrine\ORM\EntityManager;

class EventService
{
    protected $em;

    /**
     * @InjectParams({
     *    "em" = @Inject("doctrine.orm.entity_manager")
     * })
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Returns current stream
     *
     * @return StreamEntry[]
     */
    public function getCurrentStream()
    {
        $repository = $this->em->getRepository('AppBundle:StreamEntry');

        $qb = $repository->createQueryBuilder('se')
            ->where('se.start < CURRENT_TIMESTAMP()')
            ->orderBy('se.start', 'DESC')
            ->setMaxResults(1);

        $qb->andWhere('se.isApproved = :approved')
            ->setParameter('approved', 1)
            ->andWhere('se.status IN (:status)')
            ->setParameter('status', [StreamEntry::STATUS_ACTIVE, StreamEntry::STATUS_TENTATIVE]);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * Returns list of stream entries for defined time span
     *
     * @param \DateTime $dateStart
     * @param \DateTime $dateEnd
     * @param bool $approvedOnly
     * @param int|null $authorId
     * @param array $statuses
     * @return StreamEntry[]
     */
    public function getStreamEntries(\DateTime $dateStart, \DateTime $dateEnd, $approvedOnly = false, $authorId = null, $statuses = [])
    {
        $repository = $this->em->getRepository('AppBundle:StreamEntry');

        $qb = $repository->createQueryBuilder('se')
            ->where('se.start BETWEEN :dayStart AND :dayEnd')
            ->orderBy('se.start')
            ->setParameter('dayStart', $dateStart->format('Y-m-d H:i:s'))
            ->setParameter('dayEnd', $dateEnd->format('Y-m-d H:i:s'));

        if ($approvedOnly) {
            $qb->andWhere('se.isApproved = :approved')
                ->setParameter('approved', $approvedOnly);
        }

        if ($authorId) {
            $qb->andWhere('se.author = :authorId')
                ->setParameter('authorId', $authorId);
        }

        if ($statuses) {
            $qb->andWhere('se.status IN (:status)')
                ->setParameter('status', $statuses);
        }

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * @param \DateTime $date
     * @param int $step
     * @return \DateTime
     */
    public function roundMinutes(\DateTime $date, $step = 5)
    {
        $currentTime = floor($date->getTimestamp() / 100) * 100;

        $step *= 60; // Convert minutes to seconds
        $r = $currentTime % $step;

        if ($r == 0) // No need to round up
            return $date;

        $time = $currentTime + ($step - $r);

        $newDate = new \DateTime();
        $newDate->setTimestamp($time);

        return $newDate;
    }

    /**
     * @param \DateInterval $interval
     * @return int
     */
    public function durationToSeconds(\DateInterval $interval)
    {
        $reference = new \DateTimeImmutable();
        $endTime = $reference->add($interval);

        return $endTime->getTimestamp() - $reference->getTimestamp();
    }
}