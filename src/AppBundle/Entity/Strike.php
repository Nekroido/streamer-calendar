<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Strike
 *
 * @ORM\Table(name="strike")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StrikeRepository")
 */
class Strike
{
    const NOTICE = 0;
    const WARNING = 1;
    const DISMISSAL = 2;

    public static $choices = [
        'Notice' => self::NOTICE,
        'Warning' => self::WARNING,
        'Dismissal' => self::DISMISSAL,
    ];

    public static $listChoices = [
        self::NOTICE => 'Уведомление',
        self::WARNING => 'Предупреждение',
        self::DISMISSAL => 'Остранение',
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="strikes")
     * @ORM\JoinColumn(name="streamer_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $streamer;

    /**
     * @var int
     *
     * @ORM\Column(name="severity", type="smallint", options={"unsigned"=true})
     */
    private $severity;

    /**
     * @var int
     *
     * @ORM\Column(name="reason", type="text", length=65535)
     */
    private $reason;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="issued_on", type="datetime")
     */
    private $issuedOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="exprires", type="datetime")
     */
    private $expires;


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
     * Set streamer
     *
     * @param User $streamer
     *
     * @return Strike
     */
    public function setStreamer($streamer)
    {
        $this->streamer = $streamer;

        return $this;
    }

    /**
     * Get streamer
     *
     * @return User
     */
    public function getStreamer()
    {
        return $this->streamer;
    }

    /**
     * Set severity
     *
     * @param integer $severity
     *
     * @return Strike
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;

        return $this;
    }

    /**
     * Get severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * Set reason
     *
     * @param string $reason
     *
     * @return Strike
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set issuedOn
     *
     * @param \DateTime $issuedOn
     *
     * @return Strike
     */
    public function setIssuedOn($issuedOn)
    {
        $this->issuedOn = $issuedOn;

        return $this;
    }

    /**
     * Get issuedOn
     *
     * @return \DateTime
     */
    public function getIssuedOn()
    {
        return $this->issuedOn;
    }

    /**
     * Set expires
     *
     * @param \DateTime $expires
     *
     * @return Strike
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;

        return $this;
    }

    /**
     * Get expires
     *
     * @return \DateTime
     */
    public function getExpires()
    {
        return $this->expires;
    }

    public function getSeverityReadable()
    {
        return $this->severity == Strike::DISMISSAL
            ? 'Dismissal'
            : ($this->severity == Strike::WARNING ? 'Warning' : 'Notice');
    }

    public function __toString()
    {
        return $this->getSeverityReadable() . ' for ' . $this->getStreamer()->getName() . ' (' . $this->expires->format('d.m.Y H:i') . ')';
    }
}
