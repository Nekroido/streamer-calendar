<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * StreamEntry
 *
 * @ORM\Table(name="stream_entry")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StreamEntryRepository")
 */
class StreamEntry
{
    const STATUS_ACTIVE = 0;
    const STATUS_TENTATIVE = 1;
    const STATUS_CANCELLED = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="streamEntries")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="Game")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id", nullable=true)
     */
    private $game;

    /**
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="stream_participants")
     */
    private $participants;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="datetimetz")
     */
    private $start;

    /**
     * @var \DateInterval
     *
     * @ORM\Column(name="duration", type="date_interval")
     */
    private $duration;

    /**
     * @var bool
     *
     * @ORM\Column(name="all_day", type="boolean", options={"default" : 0, "unsigned"=true})
     */
    private $allDay;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint", length=1, options={"default" : 0, "unsigned"=true})
     */
    private $status = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="approved", type="boolean", options={"default" : 0, "unsigned"=true})
     */
    private $isApproved = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="added", type="datetimetz")
     */
    private $added;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

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
     * @return StreamEntry
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
     * Set dateStart
     *
     * @param \DateTime $start
     *
     * @return StreamEntry
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get dateStart
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set duration
     *
     * @param \DateInterval $duration
     *
     * @return StreamEntry
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return \DateInterval
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set allDay
     *
     * @param bool $allDay
     *
     * @return StreamEntry
     */
    public function setAllDay($allDay)
    {
        $this->allDay = $allDay;

        return $this;
    }

    /**
     * Get allDay
     *
     * @return bool
     */
    public function getAllDay()
    {
        return $this->allDay;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return StreamEntry
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param User $participant
     * @return $this
     */
    public function addParticipant(User $participant)
    {
        $this->participants[] = $participant;

        return $this;
    }

    /**
     * Remove user_recipe_associations
     *
     * @param User $participant
     */
    public function removeParticipant(User $participant)
    {
        $this->participants->removeElement($participant);
    }

    /**
     * Get user_recipe_associations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return StreamEntry
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return Project
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set author
     *
     * @param User $author
     *
     * @return StreamEntry
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return StreamEntry
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set isApproved
     *
     * @param bool $isApproved
     *
     * @return StreamEntry
     */
    public function setIsApproved($isApproved)
    {
        $this->isApproved = $isApproved;

        return $this;
    }

    /**
     * Get isConfirmed
     *
     * @return bool
     */
    public function getIsApproved()
    {
        return $this->isApproved;
    }

    /**
     * Set game
     *
     * @param Game $game
     *
     * @return StreamEntry
     */
    public function setGame($game)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        $end = clone($this->start);
        $end->add($this->duration);

        return $end;
    }

    /**
     * Get formatted title
     *
     * @return string
     */
    public function getTitleFormatted()
    {
        $title = sprintf('%s - %s', $this->getAuthor()->getName(), $this->getTitle());
        if ($this->getStatus() == self::STATUS_TENTATIVE) {
            $title = '(Возможно) ' . $title;
        }
        else if ($this->getStatus() == self::STATUS_CANCELLED) {
            $title = '(Отменён) ' . $title;
        }

        return $title;
    }

    /**
     * Set added
     *
     * @param \DateTime $added
     *
     * @return StreamEntry
     */
    public function setAdded($added)
    {
        $this->added = $added;

        return $this;
    }

    /**
     * Get added
     *
     * @return \DateTime
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return StreamEntry
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
