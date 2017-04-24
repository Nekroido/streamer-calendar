<?php
/**
 * Date: 03-Sep-16
 * Time: 10:34
 */

namespace AppBundle\Services;

use AppBundle\Entity\StreamEntry;
use AppBundle\Entity\Strike;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class StrikeService
{
    protected $em;
    protected $sm;

    /**
     * @InjectParams({
     *    "em" = @Inject("doctrine.orm.entity_manager")
     *    "sm" = @Inject("security.authorization_checker")
     * })
     * @param EntityManager $em
     * @param AuthorizationChecker $sm
     */
    public function __construct(EntityManager $em, AuthorizationChecker $sm)
    {
        $this->em = $em;
        $this->sm = $sm;
    }

    /**
     * Return list of strikes for user
     *
     * @param User $user
     * @param bool|false $excludeExpired
     * @return array
     */
    public function getStrikes(User $user, $excludeExpired = false)
    {
        $repository = $this->em->getRepository('AppBundle:Strike');

        $qb = $repository->createQueryBuilder('s')
            ->where('s.streamer = :userId')
            ->orderBy('s.expires', 'DESC')
            ->setParameter('userId', $user->getId());

        if ($excludeExpired) {
            $qb->andWhere('s.expires < :now');
            $qb->setParameter('now', new \DateTime());
        }

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * Check if user is dismissed from streams
     *
     * @param User $user
     * @return bool
     */
    public function isUserDismissed(User $user)
    {
        $repository = $this->em->getRepository('AppBundle:Strike');

        $qb = $repository->createQueryBuilder('s')
            ->where('s.expires > :now AND s.streamer = :userId AND s.severity = :severity')
            ->setParameter('now', new \DateTime())
            ->setParameter('userId', $user->getId())
            ->setParameter('severity', Strike::DISMISSAL);
        $query = $qb->getQuery();

        return $query->getResult() != null;
    }

    /**
     * Check if user can add entries
     *
     * @param User $user
     * @return bool
     */
    public function canAddEntry(User $user)
    {
        return $this->sm->isGranted('ROLE_STREAMER') || $this->isUserDismissed($user) == false;
    }

    /**
     * Check if user can edit specific entry
     *
     * @param StreamEntry $entry
     * @param User $user
     * @return bool
     */
    public function canEditEntry(StreamEntry $entry, User $user)
    {
        return $this->sm->isGranted('ROLE_MANAGER')
        ||
        ($this->sm->isGranted('ROLE_STREAMER') && $this->isUserDismissed($user) == false && $user->getId() == $entry->getAuthor()->getId());
    }

    /**
     * Check if user can edit specific entry
     *
     * @param StreamEntry $entry
     * @param User $user
     * @return bool
     */
    public function canApproveEntry(StreamEntry $entry, User $user)
    {
        return $this->sm->isGranted('ROLE_MANAGER')
        ||
        ($this->sm->isGranted('ROLE_APPROVED_STREAMER') && $this->isUserDismissed($user) == false && $user->getId() == $entry->getAuthor()->getId());
    }

    /**
     * Check if user can delete specific entry
     *
     * @param StreamEntry $entry
     * @param User $user
     * @return bool
     */
    public function canDeleteEntry(StreamEntry $entry, User $user)
    {
        return $this->sm->isGranted('ROLE_MANAGER');
    }
}