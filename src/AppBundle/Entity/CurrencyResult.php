<?php
/*
 * Entity class for storing currencies results
 * data - json array of currencies & values
 * date - data fetch date
 *
 * */
namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="currency_result")
 * @UniqueEntity("email")
 */

class CurrencyResult
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
     * Returns array, decoded from stored json
     * @return mixed
     */
    public function getData()
    {
        return json_decode($this->data);
    }

    /**
     * Save json encoded array
     *
     * @param mixed $data
     */
    public function setData(array $data)
    {
        $this->data = json_encode($data);
    }

    /**
     * Let's format our date!
     *
     * @return mixed
     */
    public function getDate()
    {
        new \DateTime();
        $timeStr = '';
        if(is_object($this->date) && $this->date instanceof \DateTime) {
            $timeStr = $this->date->format('Y-m-d H:i:s');
        } else {
            $timeStr = 'invalid date';
        }
        return $timeStr;
    }

    /**
     * @param mixed $date
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    protected $data;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     */
    protected $date;


}

?>