<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GamePlatform
 *
 * @ORM\Table(name="platform")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GamePlatformRepository")
 */
class Platform
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
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @var int
     *
     * @ORM\Column(name="giant_bomb_id", type="integer", options={"unsigned"=true})
     */
    private $giantBombId;


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
     * Set title
     *
     * @param string $title
     *
     * @return Platform
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set giantBombId
     *
     * @param int $giantBombId
     *
     * @return Platform
     */
    public function setGiantBombId($giantBombId)
    {
        $this->giantBombId = $giantBombId;

        return $this;
    }

    /**
     * Get giantBombId
     *
     * @return string
     */
    public function getGiantBombId()
    {
        return $this->giantBombId;
    }
}
