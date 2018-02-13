<?php
/**
 * Created by PhpStorm.
 * User: sovkutsan
 * Date: 2/13/18
 * Time: 9:57 AM
 */

/*
 * Custom console command for fetching & parsing xml with currencies values
 */

namespace AppBundle\Command;

use AppBundle\Entity\Setting;
use AppBundle\Repository\SettingRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Service\CurrencyParser;
use Doctrine\Bundle\DoctrineBundle\Registry;

class CustomCommand extends Command
{
    private $doctrine;

    /**
     * CustomCommand constructor.
     * This is a service, so a want to get Doctrine here through constructor
     * @param Registry $doctrine
     */
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

        $period = (int) $this->doctrine->getRepository(Setting::class)->getSetting('currency_update_period');
        $lastUpdate = (int) $this->doctrine->getRepository(Setting::class)->getSetting('currency_last_update');

        $period *= 60;

        var_dump($period);
        var_dump('lastUpdate - ' . $lastUpdate);
        var_dump('currentTime - ' . time());
        var_dump('dT - ' . (time() - $lastUpdate));


        if( (time() - $lastUpdate) < $period) {
            $oFace->writeln('It\'s not time for this');
            return;
        }

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