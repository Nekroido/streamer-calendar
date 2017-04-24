<?php
/**
 * Date: 20-Aug-16
 * Time: 11:59
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity="StreamEntry", mappedBy="author")
     */
    private $streamEntries;

    /**
     * @ORM\OneToMany(targetEntity="Strike", mappedBy="streamer")
     */
    private $strikes;

    /**
     * @ORM\Column(type="datetime")
     */
    private $registered;

    /**
     * @ORM\Column(name="can_see_global_streamkey", type="boolean", options={"default" : 0, "unsigned"=true})
     */
    private $canSeeGlobalStreamkey = false;

    /**
     * @ORM\Column(type="string", options={"default" : ""})
     */
    private $personalStreamkey;

    /**
     * @ORM\Column(type="string", options={"default" : ""})
     */
    private $donationUrl;

    /**
     * @ORM\Column(type="string", options={"default" : ""})
     */
    private $pseudonyms;

    /**
     * @ORM\Column(type="string", options={"default" : ""})
     */
    private $likes;

    /**
     * @ORM\Column(type="string", options={"default" : ""})
     */
    private $preferredPlatforms;

    /**
     * @ORM\Column(type="text", length=65535, options={"default" : ""})
     */
    private $about;

    /**
     * @ORM\Column(type="string", options={"default" : ""})
     */
    private $motto;

    /**
     * @Vich\UploadableField(mapping="avatar_image", fileNameProperty="imageName")
     *
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $imageName;

    /* Registration related */
    public $token;

    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    /**
     * @return \DateTime
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * @param \DateTime $registered
     */
    public function setRegistered(\DateTime $registered)
    {
        $this->registered = $registered;
    }

    /**
     * Returns the roles or permissions granted to the user for security.
     */
    public function getRoles()
    {
        $roles = $this->roles;
        // guarantees that a user always has at least one role for security
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }
        return array_unique($roles);
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->streamEntries = new ArrayCollection();
        $this->strikes = new ArrayCollection();
    }

    /**
     * Add streamEntry
     *
     * @param StreamEntry $streamEntry
     *
     * @return User
     */
    public function addStreamEntry(StreamEntry $streamEntry)
    {
        $this->streamEntries[] = $streamEntry;

        return $this;
    }

    /**
     * Remove streamEntry
     *
     * @param StreamEntry $streamEntry
     */
    public function removeStreamEntry(StreamEntry $streamEntry)
    {
        $this->streamEntries->removeElement($streamEntry);
    }

    /**
     * Get streamEntries
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStreamEntries()
    {
        return $this->streamEntries;
    }

    /**
     * Add strike
     *
     * @param Strike $strike
     * @return User
     */
    public function addStrike(Strike $strike)
    {
        $this->strikes[] = $strike;

        return $this;
    }

    /**
     * Remove strike
     *
     * @param Strike $strike
     */
    public function removeStrike(Strike $strike)
    {
        $this->strikes->removeElement($strike);
    }

    /**
     * Get strikes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStrikes()
    {
        return $this->strikes;
    }

    /**
     * Set canSeeGlobalStreamkey
     *
     * @param bool $canSeeGlobalStreamkey
     *
     * @return StreamEntry
     */
    public function setCanSeeGlobalStreamkey($canSeeGlobalStreamkey)
    {
        $this->canSeeGlobalStreamkey = $canSeeGlobalStreamkey;

        return $this;
    }

    /**
     * Get canSeeGlobalStreamkey
     *
     * @return bool
     */
    public function getCanSeeGlobalStreamkey()
    {
        return $this->canSeeGlobalStreamkey;
    }

    /**
     * Set personalStreamkey
     *
     * @param string $personalStreamkey
     *
     * @return StreamEntry
     */
    public function setPersonalStreamkey($personalStreamkey)
    {
        $this->personalStreamkey = $personalStreamkey;

        return $this;
    }

    /**
     * Get personalStreamkey
     *
     * @return string
     */
    public function getPersonalStreamkey()
    {
        return $this->personalStreamkey;
    }

    /**
     * Set donationUrl
     *
     * @param string $donationUrl
     *
     * @return StreamEntry
     */
    public function setDonationUrl($donationUrl)
    {
        $this->donationUrl = $donationUrl;

        return $this;
    }

    /**
     * Get donationUrl
     *
     * @return string
     */
    public function getDonationUrl()
    {
        return $this->donationUrl;
    }

    /**
     * @return string
     */
    public function getPseudonyms()
    {
        return $this->pseudonyms;
    }

    /**
     * @param string $pseudonyms
     * @return User
     */
    public function setPseudonyms($pseudonyms)
    {
        $this->pseudonyms = $pseudonyms;
        return $this;
    }

    /**
     * @return string
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * @param string $likes
     * @return User
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;
        return $this;
    }

    /**
     * @return string
     */
    public function getPreferredPlatforms()
    {
        return $this->preferredPlatforms;
    }

    /**
     * @param string $preferredPlatforms
     * @return User
     */
    public function setPreferredPlatforms($preferredPlatforms)
    {
        $this->preferredPlatforms = $preferredPlatforms;
        return $this;
    }

    /**
     * Set about
     *
     * @param string $about
     *
     * @return User
     */
    public function setAbout($about)
    {
        $this->about = $about;

        return $this;
    }

    /**
     * Get about
     *
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * @return string
     */
    public function getMotto()
    {
        return $this->motto;
    }

    /**
     * @param string $motto
     * @return User
     */
    public function setMotto($motto)
    {
        $this->motto = $motto;
        return $this;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return User
     */
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * @param string $imageName
     * @return User
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersistSetRegistered()
    {
        $this->registered = new \DateTime();
    }

    public function __toString()
    {
        return $this->getUsername();
    }

    public function __sleep()
    {
        $vars = get_object_vars($this);
        unset($vars['imageFile']);
        return array_keys($vars);
    }
}
