<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dashboard
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DashboardRepository")
 */
class Dashboard
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="media", type="string", length=255, nullable=true)
     */
    private $media;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="broadcast_start", type="datetimetz")
     */
    private $broadcastStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="broadcast_end", type="datetimetz", nullable=true)
     */
    private $broadcastEnd;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"default" : 0, "unsigned"=true})
     */
    private $viewers;

    /**
     * @var int
     *
     * @ORM\Column(name="max_viewers", type="integer", options={"default" : 0, "unsigned"=true})
     */
    private $maxViewers;

    /**
     * @var int
     *
     * @ORM\Column(name="is_live", type="boolean", options={"default" : 0, "unsigned"=true})
     */
    private $isLive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="`current_time`", type="datetimetz")
     */
    private $currentTime;

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
     * @return Project
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Project $category
     * @return Dashboard
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Dashboard
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
     * Set media
     *
     * @param string $media
     *
     * @return Dashboard
     */
    public function setMedia($media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return string
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Set broadcastStart
     *
     * @param \DateTime $broadcastStart
     *
     * @return Dashboard
     */
    public function setBroadcastStart($broadcastStart)
    {
        $this->broadcastStart = $broadcastStart;

        return $this;
    }

    /**
     * Get broadcastStart
     *
     * @return \DateTime
     */
    public function getBroadcastStart()
    {
        return $this->broadcastStart;
    }

    /**
     * @return \DateTime
     */
    public function getBroadcastEnd()
    {
        return $this->broadcastEnd;
    }

    /**
     * @param \DateTime $broadcastEnd
     * @return Dashboard
     */
    public function setBroadcastEnd($broadcastEnd)
    {
        $this->broadcastEnd = $broadcastEnd;
        return $this;
    }

    /**
     * @return int
     */
    public function getViewers()
    {
        return $this->viewers;
    }

    /**
     * @param int $viewers
     * @return Dashboard
     */
    public function setViewers($viewers)
    {
        $this->viewers = $viewers;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxViewers()
    {
        return $this->maxViewers;
    }

    /**
     * @param int $maxViewers
     * @return Dashboard
     */
    public function setMaxViewers($maxViewers)
    {
        $this->maxViewers = $maxViewers;
        return $this;
    }

    /**
     * @return int
     */
    public function getIsLive()
    {
        return $this->isLive;
    }

    /**
     * @param int $isLive
     * @return Dashboard
     */
    public function setIsLive($isLive)
    {
        $this->isLive = $isLive;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCurrentTime()
    {
        return $this->currentTime;
    }

    /**
     * @param \DateTime $currentTime
     * @return Dashboard
     */
    public function setCurrentTime($currentTime)
    {
        $this->currentTime = $currentTime;
        return $this;
    }
}

