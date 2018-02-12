<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="user")user
 * @UniqueEntity("email")
 * @UniqueEntity("name")
 */
//class User implements UserInterface, \Serializable
class User extends BaseUser
{

    public function __construct() {
        parent::__construct();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function encryptPassword()
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param mixed $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        if ($this->name == 'admin') {
            return ['ROLE_ADMIN'];
        }
        return ['ROLE_USER'];
    }

    public function eraseCredentials()
    {
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
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
    public function getUsername()
    {
        return $this->name;
    }

    public function getName()
    {
        return $this->getUsername();
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(groups={"registration"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Email(groups={"registration"})
     */
    private $email;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(min=18, max=110, minMessage="You should be at least 18!", maxMessage="You are too old for this shit. Sorry.")
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(min=6, minMessage="Password is too short")
     */
    private $password;

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([
            $this->id, $this->name, $this->password
        ]);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list($this->id, $this->name, $this->password) = unserialize($serialized);
    }


    public function isNew()
    {
        return empty($this->name) && empty($this->password);
    }
}

?>