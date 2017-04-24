<?php
/**
 * Date: 02-Sep-16
 * Time: 10:44
 */

namespace AppBundle\Services;

use AppBundle\Entity\Token;
use Doctrine\ORM\EntityManager;

class TokenService
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
     * @param string $token
     * @param bool $isUsed
     * @return Token
     */
    public function isValid($token, $isUsed = false)
    {
        $token = $this->em->getRepository('AppBundle:Token')->findOneBy([
            'token' => $token,
            'isUsed' => $isUsed
        ]);

        return $token;
    }
}