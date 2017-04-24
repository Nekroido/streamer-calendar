<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaskStatus
 *
 * @ORM\Table(name="task_status")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaskStatusRepository")
 */
class TaskStatus
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, unique=true)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_run", type="datetimetz")
     */
    private $lastRun;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_success", type="boolean")
     */
    private $isSuccess;


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
     * Set type
     *
     * @param string $type
     *
     * @return TaskStatus
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set lastRun
     *
     * @param \DateTime $lastRun
     *
     * @return TaskStatus
     */
    public function setLastRun($lastRun)
    {
        $this->lastRun = $lastRun;

        return $this;
    }

    /**
     * Get lastRun
     *
     * @return \DateTime
     */
    public function getLastRun()
    {
        return $this->lastRun;
    }

    /**
     * Set isSuccess
     *
     * @param boolean $isSuccess
     *
     * @return TaskStatus
     */
    public function setIsSuccess($isSuccess)
    {
        $this->isSuccess = $isSuccess;

        return $this;
    }

    /**
     * Get isSuccess
     *
     * @return bool
     */
    public function getIsSuccess()
    {
        return $this->isSuccess;
    }
}

