<?php
/**
 * Created by PhpStorm.
 * User: sovkutsan
 * Date: 2/13/18
 * Time: 9:57 AM
 */

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Service\CurrencyParser;
use Doctrine\Bundle\DoctrineBundle\Registry;

class CustomCommand extends Command
{
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        parent::__construct('custom:get-currency');
    }

    protected function configure()
    {
        $this
            ->setName('custom:get-currency')
            ->setDescription('Receives data from outside')
            ->setHelp('This command allow you to get some currencies from the internet')
            ->addArgument('code', InputArgument::OPTIONAL, 'Currency ISO code. All currencies, if empty.');
    }

    protected function execute(InputInterface $iFace, OutputInterface $oFace)
    {
        $oFace->writeln('Parsing...');
        $src = file_get_contents('http://www.nbkr.kg/XML/daily.xml');
        $code = $iFace->getArgument('code');

        $parser = new CurrencyParser($this->doctrine, $src, $code);

        try {
            $parser->parse();
        } catch (\Exception $e) {
            die;
        }

        $result = $parser->getResult();
        $oFace->writeln($result);
    }

}