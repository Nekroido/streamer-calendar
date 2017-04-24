<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Token
 *
 * @ORM\Table(name="token")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TokenRepository")
 */
class Token
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=32, unique=true)
     */
    private $token;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_used", type="boolean", options={"default" : 0, "unsigned"=true})
     */
    private $isUsed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="used_at", type="datetime", nullable=true)
     */
    private $usedAt;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="used_by", referencedColumnName="id", onDelete="CASCADE")
     */
    private $usedBy;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return Token
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set isUsed
     *
     * @param boolean $isUsed
     *
     * @return Token
     */
    public function setIsUsed($isUsed)
    {
        $this->isUsed = $isUsed;

        return $this;
    }

    /**
     * Get isUsed
     *
     * @return bool
     */
    public function getIsUsed()
    {
        return $this->isUsed;
    }

    /**
     * Set usedAt
     *
     * @param \DateTime $usedAt
     *
     * @return Token
     */
    public function setUsedAt($usedAt)
    {
        $this->usedAt = $usedAt;

        return $this;
    }

    /**
     * Get usedAt
     *
     * @return \DateTime
     */
    public function getUsedAt()
    {
        return $this->usedAt;
    }

    /**
     * Set usedBy
     *
     * @param User $usedBy
     *
     * @return Token
     */
    public function setUsedBy($usedBy)
    {
        $this->usedBy = $usedBy;

        return $this;
    }

    /**
     * Get usedBy
     *
     * @return User
     */
    public function getUsedBy()
    {
        return $this->usedBy;
    }

    public function __toString()
    {
        return $this->getToken();
    }
}