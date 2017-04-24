<?php
/**
 * Created by PhpStorm.
 * User: nekro
 * Date: 22-Dec-16
 * Time: 16:05
 */

namespace AppBundle\Services;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class UserService
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
     * @return User[]
     */
    public function getRandomUserWithDonation()
    {
        $repository = $this->em->getRepository('AppBundle:User');

        $qb = $repository->createQueryBuilder('u')
            ->where('u.donationUrl <> \'\'')
            ->orderBy('RAND()')
            ->setMaxResults(1);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * @return User[]
     */
    public function getUsersWithDonation()
    {
        $repository = $this->em->getRepository('AppBundle:User');

        $qb = $repository->createQueryBuilder('u')
            ->where('u.donationUrl <> \'\'')
            ->orderBy('u.name');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * @return User[]
     */
    public function getStreamers()
    {
        $repository = $this->em->getRepository('AppBundle:User');

        $qb = $repository->createQueryBuilder('u');
        $qb->where('u.roles <> \'ROLE_USER\'')
            ->orderBy('u.roles')
            ->addOrderBy('u.name');

        $query = $qb->getQuery();

        return $query->getResult();
    }
}