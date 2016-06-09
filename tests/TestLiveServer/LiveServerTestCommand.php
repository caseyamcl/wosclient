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
use WosClient\Exception\WosServerException;

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
            $objId = $wosClient->putObject('test object', ['foo' => 'bar']);
            $io->success('Wrote object with ID: ' . (string) $objId);

            $wosObj = $wosClient->getObject($objId);
            $io->success("Retrieved object with ID: {$wosObj->getId()}; Data: " . $wosObj->getData());

            $wosClient->deleteObject($objId);
            $io->success('Deleted object with ID: ' . (string) $objId);

            try {
                $wosClient->getObject($objId);
            }
            catch (WosServerException $e) {
                $io->success(sprintf('Received appropriate error response for non-existent object: %s (%s)',
                    (string) $objId,
                    (string) $e
                ));
            }

            $objId = $wosClient->reserveObject();
            $io->success('Reserved object ID: ' . (string) $objId);

            $objId = $wosClient->putObject('another test object', ['foo' => 'bar'], $objId);
            $io->success('Wrote object with previously reserved ID: ' . (string) $objId);

            try {
                $wosClient->putObject('should not work', [], $objId);
            }
            catch (WosServerException $e) {
                $io->success(sprintf(
                    'Received appropriate response when attempting to write object ID twice: %s (%s)',
                    (string) $objId,
                    (string) $e
                ));
            }

            $metadata = $wosClient->getMetadata($objId);
            $io->success('Got metadata for object with ID: ' . (string) $objId . '; Data: ' . json_encode($metadata->toArray()));

            $wosClient->deleteObject($objId);
            $io->success('Deleted object with ID: ' . (string) $objId);
        }
        catch (\Exception $e) {
            $io->error($e->getMessage());
        }

    }




}
