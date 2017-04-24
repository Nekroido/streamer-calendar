<?php
/**
 * Created by PhpStorm.
 * User: nekro
 * Date: 22-Jan-17
 * Time: 15:17
 */

namespace AppBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class UserProfileType
 * @Vich\Uploadable
 * @package AppBundle\Entity
 */
class UserProfileType
{
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="avatar_image", fileNameProperty="avatarFileName")
     *
     * @var File
     */
    public $avatarFile;

    public $avatarFileName;

    public $pseudonyms;

    public $likes;

    public $preferredPlatforms;

    public $about;

    public $motto;
}