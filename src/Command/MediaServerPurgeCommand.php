<?php

/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Command;

use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ynlo\RestfulPlatformBundle\MediaServer\MediaFileInterface;

class MediaServerPurgeCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('restful_platform:media_server:purge')
            ->setDescription('Remove unused uploaded media files from database and storage');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $class = $this->getContainer()->getParameter('restful_platform.config.media_server')['class'] ?? '';

        if (!$class) {
            throw  new \Exception('The option `restful_platform.config.media_server.class` is invalid.');
        }

        $storagePool = $this->getContainer()->get('restful_platform.media_storage_pool');


        /** @var EntityRepository $repo */
        $repo = $em->getRepository($class);

        $results = $repo
            ->createQueryBuilder('o')
            ->where('o.status = :status')
            ->andWhere('o.createdAt < :date')
            ->setParameter('status', MediaFileInterface::STATUS_NEW)
            ->setParameter('date', (new \DateTime())->modify('-5Hours'))//TODO: should be configured
            ->getQuery()
            ->getResult();

        $count = count($results);

        if (!$count) {
            $output->writeln('Media server is clean, all files are in use.');

            return;
        }

        $output->writeln(sprintf('%s new files will be deleted', $count));
        $output->writeln('Deleting....');

        $errors = 0;

        /** @var MediaFileInterface $result */
        foreach ($results as $result) {
            $provider = $storagePool->getByStorageId($result->getStorage());
            try {
                $provider->remove($result);
                $em->remove($result);
                $output->writeln(sprintf('[DELETED] File: %s', $result->getUuid()));
            } catch (\Exception $exception) {
                $errors++;
                $output->writeln(sprintf('[ERROR] File: %s, ERROR: %s', $result->getUuid(), $exception->getMessage()));
            }
        }

        if (!$errors) {
            $output->writeln('All files has been deleted successfully');
        } else {
            $output->writeln('Not all files has been delete, check the logs');
        }

        $em->flush();
    }
}
