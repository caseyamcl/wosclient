<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 6/9/16
 * Time: 10:44 AM
 */

namespace WosClient\TestLiveServer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WosClient\WosClient;

/**
 * Live Test
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class LiveServerTestCommand extends Command
{
    const COMMAND_NAME = 'wos:test';

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription('Run automated tests against a live WOS server');
        $this->addArgument('url',    InputArgument::REQUIRED, 'The URL for the WOS server');
        $this->addArgument('policy', InputArgument::REQUIRED, 'Your WOS policy ID or name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url    = $input->getArgument('url');
        $policy = $input->getArgument('policy');

        $io = new SymfonyStyle($input, $output);

        $io->title('WOS Client Tests');
        $io->section("Testing <info>$url</info> with policy: <info>$policy</info>");

        $wosClient = WosClient::build($url, $policy);

        try {
            $wosId = $wosClient->putObject('test object', ['foo' => 'bar']);
            $io->success('Wrote object with ID: ' . $wosId->getObjectId());

            $wosObj = $wosClient->getObject($wosId);
            $io->success("Retrieved object with ID: {$wosObj->getId()}; Data: " . $wosObj->getData());

            $resp = $wosClient->deleteObject($wosId);
            $io->success('Deleted object with ID: ' . (string) $wosId);

        }
        catch (\Exception $e) {
            $io->error($e->getMessage());
        }

    }




}
