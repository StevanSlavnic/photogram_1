<?php
/**
 * Created by PhpStorm.
 * User: stevan
 * Date: 6/15/16
 * Time: 11:18 PM
 */

namespace AppBundle\Entity;

use AppBundle\Entity\User;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Form\UserType;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(name="profile")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProfileRepository")
 * @Vich\Uploadable
 *
 */
class Profile
{
    /**
    * @ORM\Id
    * @ORM\Column(name="id", type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    *
    */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="firstname", type="string")
     */
    protected $firstname;

    /**
     * @var string
     * @ORM\Column(name="lastname", type="string")
     */
    protected $lastname;

//    /**
//     * @Gedmo\Slug(fields={"profileFullName"}, updatable=false, separator="-")
//     * @ORM\Column(length=128, unique=true, nullable=false)
//     */
//    protected $username;

    /**
    * @Gedmo\Slug(fields={"firstname", "lastname"}, updatable=false, separator="-")
    * @ORM\Column(length=128, unique=true, nullable=false)
    */
    protected $profileFullName;

    /**
     * @var string
     * @ORM\Column(name="occupation", type="string", nullable=true)
     */
    private $occupation;

    /**
     * @var string
     * @ORM\Column(name="about", type="string", nullable=true)
     */
    private $about;


    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $updatedAt;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="profile_image", fileNameProperty="imageName")
     *
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $imageName;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

//    /**
//     * @return mixed
//     */
//    public function getUsername()
//    {
//        return $this->username;
//    }
//
//    /**
//     * @param mixed $username
//     */
//    public function setUsername($username)
//    {
//        $this->username = $username;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getProfileFullName()
//    {
//        return $this->profileFullName;
//    }
//
//    /**
//     * @param mixed $profileFullName
//     */
//    public function setProfileFullName($profileFullName)
//    {
//        $this->profileFullName = $profileFullName;
//    }

    /**
     * @return string
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * @param string $occupation
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;
    }

    /**
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * @param string $about
     */
    public function setAbout($about)
    {
        $this->about = $about;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return Profile
     */
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param string $imageName
     *
     * @return Post
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }
}
