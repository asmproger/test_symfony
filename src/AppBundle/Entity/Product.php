<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="product")
 */
class Product
{

    const PATH_TO_UPLOADED = '/var/www/first/web/uploads/images/';

    private $file;

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }
        $newName = md5(time()) . '.' . $this->getFile()->guessExtension();

        $this->getFile()->move(
            self::PATH_TO_UPLOADED,
            $newName
        );

        $this->pic = $newName;
        $this->setFile(null);
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function lifecycleFileUpload() {
        $this->upload();
    }
    public function refreshUpdated() {
        //$this->setUpdated(new \DateTime());
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @return mixed
     */
    public function getPic()
    {
        return $this->pic;
    }

    public function getPicPath() {
        return 'uploads/images/' . $this->pic;
    }

    /**
     * @param mixed $pic
     */
    public function setPic($pic)
    {
        $this->pic = $pic;
    }

    /**
     * @ORM\Column(type="decimal", scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(type="double", message="Not valid number")
     * @Assert\Range(min=1, max = 9999, minMessage = "Price cannot be 0", maxMessage="Max price can be less or equal 9999")
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $description;


    /**
     * @ORM\Column(type="text")
     */
    private $pic;
}

?>