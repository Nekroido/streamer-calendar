<?php
/**
 * Date: 28-Aug-16
 * Time: 13:39
 */

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints\DateTime;

class EntryType
{
    public $title;
    public $comment;

    /**
     * @var Project
     */
    public $category;

    /**
     * @var User
     */
    public $author;

    public $dateStart;
    public $timeStart;

    public $dateEnd;
    public $timeEnd;

    public $allDay;

    public $status;
    public $description;

    public $isApproved = false;

    public $isApprovedStreamer = false;

    /**
     * @var StreamEntry
     */
    private $entry;

    public function __construct(StreamEntry $entry)
    {
        $this->entry = $entry;
        $this->madData();
    }

    private function madData()
    {
        $start = $this->entry->getStart();
        $end = clone($start);
        $end->add($this->entry->getDuration());

        $this->title = $this->entry->getTitle();
        $this->comment = $this->entry->getComment();
        $this->category = $this->entry->getCategory();
        $this->author = $this->entry->getAuthor();
        $this->dateStart = $start->format('d.m.Y');
        $this->timeStart = $start->format('H:i');
        $this->dateEnd = $end->format('d.m.Y');
        $this->timeEnd = $end->format('H:i');
        $this->allDay = $this->entry->getAllDay();
        $this->status = $this->entry->getStatus();
        $this->isApproved = $this->entry->getIsApproved();
        $this->description = $this->entry->getDescription();
    }

    public function mapEntry()
    {
        $start = new \DateTime($this->dateStart . ' ' . ($this->allDay ? '00:00' : $this->timeStart) . ':00');
        $end = new \DateTime(($this->allDay ? $this->dateStart : $this->dateEnd) . ' ' . ($this->allDay ? '23:59:59' : $this->timeEnd . ':00'));
        $duration = $start->diff($end);

        $this->entry->setTitle($this->title);
        $this->entry->setComment($this->comment);
        $this->entry->setAuthor($this->author);
        $this->entry->setCategory($this->category);
        $this->entry->setStart($start);
        $this->entry->setDuration($duration);
        $this->entry->setAllDay($this->allDay);
        $this->entry->setStatus($this->status);
        $this->entry->setIsApproved($this->isApproved);
        $this->entry->setDescription($this->description);

        return $this->entry;
    }
}