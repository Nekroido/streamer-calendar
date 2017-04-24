<?php

namespace AppBundle\Command;

use AppBundle\Entity\TaskStatus;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DailyAnnounceCommand extends ContainerAwareCommand
{
    protected $taskType = 'daily-announce';

    protected function configure()
    {
        $this
            ->setName('daily-announce')
            ->setDescription('Perform daily stream schedule announcement.')
            //->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            //->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /*$argument = $input->getArgument('argument');

        if ($input->getOption('option')) {
            // ...
        }*/

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $em->getRepository('AppBundle:TaskStatus');

        $qb = $repository->createQueryBuilder('ts')
            ->where('ts.type = :type')
            ->setMaxResults(1)
            ->setParameter('type', $this->taskType);
        $query = $qb->getQuery();

        $task = $query->getOneOrNullResult();

        if ($this->canRun($task)) {
            $output->writeln('Gathering data...');
            $isSuccess = $this->getContainer()->get('app.vk_service')->performDailyAnnounce();

            if ($task == null)
                $task = new TaskStatus();

            $task->setType($this->taskType);
            $task->setLastRun(new \DateTime());
            $task->setIsSuccess($isSuccess);

            $em->merge($task);
            $em->flush();
            $output->writeln('Announce was posted successfully.');
        } else {
            $output->writeln('Already ran today.');
        }
    }

    /**
     * @param TaskStatus $task
     * @return bool
     */
    private function canRun($task)
    {
        if ($task == null)
            return true;

        $nextRun = $task->getLastRun();

        $currentTime = new \DateTime();

        $difference = $currentTime->getTimestamp() - $nextRun->getTimestamp();

        if ($difference >= 84600 && $task->getIsSuccess()) {
            // More than 23 hours 30 minutes elapsed since last successful launch
            return true;
        } else if ($task->getIsSuccess() == false) {
            // Last run was unsuccessful
            return true;
        } else
            return false;
    }

}
