<?php
/*
 * XML parser class (using DOM Element)
 *
 * parse & store currencies values
 *
 */
namespace AppBundle\Service;


use AppBundle\Entity\CurrencyResult;
use AppBundle\Entity\Setting;
use Doctrine\Bundle\DoctrineBundle\Registry;

class CurrencyParser
{
    private $src;
    private $document;
    private $result;
    private $code;

    private $doctrine;

    public function __construct(Registry $doctrine, $src, $code = '')
    {
        $this->doctrine = $doctrine;
        $this->src = $src;
        $this->code = $code;
    }

    public function parse($save = true)
    {
        $this->document = new \DOMDocument();
        if (!$this->document->loadXML($this->src)) {
            throw new \Exception('Invalid xml');
        }

        $this->result = [];
        /**
         * @var \DOMElement $item
         */
        /*
         * Parse xml - one row or all, depends from $this->code
         * */
        foreach ($this->document->getElementsByTagName('Currency') as $item) {
            $value = $item->getElementsByTagName('Value')[0]->textContent;
            $currency = $item->getAttribute('ISOCode');
            if (!empty($this->code) && $this->code == mb_strtolower($currency)) {
                $this->result[] = "{$currency}: {$value}";
                break;
            } else {
                $this->result[] = "{$currency}: {$value}";
            }
        }

        /*
         * Saving parse result in db, if $save is true
         * */
        if($save) {
            $em = $this->doctrine->getManager();
            $connection = $em->getConnection();
            $connection->beginTransaction();
            try {
                $row = new CurrencyResult();
                $row->setData($this->result);
                $row->setDate(new \DateTime());

                $em->persist($row);
                $em->flush();
                $connection->commit();
            } catch (\Exception $e) {
                // silent exception
                $connection->rollback();
            }
        }
        $this->doctrine->getRepository(Setting::class)->setSetting('currency_last_update ', time());
    }

    /**
     * Just returns parsing result (or exception)
     *
     * */
    public function getResult()
    {
        if (!$this->result) {
            throw new \Exception('You try to get result without parsing!');
        }

        return $this->result;
    }

}
