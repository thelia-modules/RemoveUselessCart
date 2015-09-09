<?php

namespace RemoveUselessCart\Command;

use DateTime;
use RemoveUselessCart\Event\RemoveUselessCartEvent;
use RemoveUselessCart\Event\RemoveUselessCartEvents;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Thelia\Command\ContainerAwareCommand;

/**
 * Class RemoveUselessCartCommand
 * @package RemoveUselessCart\Command
 * @author Etienne Perriere - OpenStudio <eperriere@openstudio.fr>
 */
class RemoveUselessCartCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName("carts:remove")
            ->setDescription("Remove useless carts")
            ->addArgument(
                'start_date',
                InputArgument::OPTIONAL,
                '[required if --day option is not used] yyyy-mm-dd date from which you want to remove carts.',
                null
            )
            ->addArgument(
                'start_time',
                InputArgument::OPTIONAL,
                '[optional] hh:mm:ss time of the given date you want to remove carts from.',
                null
            )
            ->addOption(
                'all',
                'a',
                InputOption::VALUE_NONE,
                'Set this option if you want to remove all carts from the given date, event those with products.'
            )
            ->addOption(
                'day',
                'd',
                InputOption::VALUE_REQUIRED,
                '[Must not be used with \'start_date\'] tell from how many days ago carts have to be removed.',
                null
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $startDate = $this->checkInput($input, $output)) {
            return;
        }

        try {
            $output->writeln('This may take a while with huge databases and a distant date. Please have a coffee and wait.');

            // Build event from command line data & dispatch it
            $event = new RemoveUselessCartEvent(
                $startDate,
                ($input->getOption('all')) ? true : false,
                $output
            );
            $this->getDispatcher()->dispatch(RemoveUselessCartEvents::REMOVE_USELESS_CARTS, $event);

            // Get number of removed carts
            $removeCarts = $event->getRemovedCarts();

            $output->writeln("<info>Successfully removed $removeCarts carts</info>");
        } catch (\Exception $e) {
            $output->writeln("<error>Error</error>");
            $output->writeln($e);
        }
    }

    /**
     * Check if the input value is correct
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null|string
     */
    protected function checkInput(InputInterface $input, OutputInterface $output)
    {
        // Get inputted days
        if (null !== $days = $input->getOption('day')) {
            // Check if the date isn't too close
            if ($days <= 2) {
                // Prompt a confirmation message
                $dialog = $this->getHelper('dialog');

                if (!$dialog->askConfirmation(
                    $output,
                    '<question>This is a very short range, current customers\' carts might be removed! Do you really want to continue? (y|N) </question>',
                    false
                )
                ) {
                    return null;
                }
            }

            // Create date from inputted days
            $startDate = date('Y-m-d', strtotime("- $days days"));
        } elseif (null !== $startDate = $input->getArgument('start_date')) {
            // Get inputted date

            // Check if the date is a correct date
            if ($this->validateDate($startDate) === true) {
                // Get inputted time if there is one
                if ($startTime = $input->getArgument('start_time')) {
                    $startDate .= ' '.$startTime;

                    // Check if the time is a correct one
                    if ($this->validateDateTime($startDate) !== true) {
                        // Prompt wrong time format
                        $output->writeln('<comment>Wrong time format. Time should look like \'hh:mm:ss\'</comment>');
                        return null;
                    }
                }
            } else {
                // Prompt wrong date format
                $output->writeln('<comment>Wrong date format. Date should look like \'yyyy-mm-dd\'</comment>');
                return null;
            }
        } else {
            $output->writeln("<comment>No argument nor option</comment>");
            return null;
        }

        return $startDate;
    }

    /**
     * Check if the date is valid
     *
     * @param $date
     * @return bool
     */
    protected function validateDate($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d instanceof DateTime  && $d->format('Y-m-d') == $date;
    }

    /**
     * check if the datetime is valid
     *
     * @param $datetime
     * @return bool
     */
    protected function validateDateTime($datetime)
    {
        $dt = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
        return $dt instanceof DateTime && $dt->format('Y-m-d H:i:s') == $datetime;
    }
}
