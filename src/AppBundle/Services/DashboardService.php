<?php
/**
 * Created by PhpStorm.
 * User: nekro
 * Date: 21-Feb-17
 * Time: 12:39
 */

namespace AppBundle\Services;


use AppBundle\Entity\Dashboard;
use Doctrine\ORM\EntityManager;

class DashboardService
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
     * @param \DateTime $dateStart
     * @param \DateTime $dateEnd
     * @param int|null $projectId
     * @return Dashboard[]
     */
    public function getDashboards($dateStart, $dateEnd, $projectId = null)
    {
        $repository = $this->em->getRepository('AppBundle:Dashboard');

        $qb = $repository->createQueryBuilder('d')
            ->where('d.currentTime BETWEEN :dayStart AND :dayEnd')
            ->orderBy('d.currentTime', 'ASC')
            ->setParameter('dayStart', $dateStart->format('Y-m-d H:i:s'))
            ->setParameter('dayEnd', $dateEnd->format('Y-m-d H:i:s'));

        if ($projectId) {
            $qb->andWhere('d.category = :projectId')
                ->setParameter('projectId', $projectId);
        }

        $query = $qb->getQuery();

        return $query->getResult();
    }
}