<?php

namespace RemoveUselessCart\Command;

use DateTime;
use RemoveUselessCart\Event\RemoveUselessCartEvent;
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
                    return;
                }
            }

            // Create date from inputted days
            $startDate = date('Y-m-d', strtotime("- $days days"));
        } elseif (null !== $startDate = $input->getArgument('start_date')) { // Get inputted date

            // Check if the date is a correct date
            $isDate = $this->validateDate($startDate);

            if ($isDate === true) {
                // Get inputted time if there is one
                if ($startTime = $input->getArgument('start_time')) {
                    $startDate .= ' '.$startTime;

                    // Check if the time is a correct one
                    $isDateTime = $this->validateDateTime($startDate);

                    if ($isDateTime !== true) {
                        // Prompt wrong time format
                        $output->writeln('<comment>Wrong time format. Time should look like \'hh:mm:ss\'</comment>');
                        return;
                    }
                }
            } else {
                // Prompt wrong date format
                $output->writeln('<comment>Wrong date format. Date should look like \'yyyy-mm-dd\'</comment>');
                return;
            }
        } else {
            $output->writeln("<comment>No argument nor option</comment>");
            return;
        }


        // Get option
        $removeAll = false;

        if ($input->getOption('all')) {
            $removeAll = true;
        }

        try {
            // Build event from command line data & dispatch it
            $event = new RemoveUselessCartEvent($startDate, $removeAll);
            $this->getDispatcher()->dispatch('remove-useless-carts', $event);

            // Get number of removed carts
            $removeCarts = $event->getRemovedCarts();

            $output->writeln("<info>Successfully removed $removeCarts carts</info>");
        } catch (\Exception $e) {
            $output->writeln("<error>Error</error>");
            $output->writeln($e);
        }
    }

    /**
     * Check if a date is valid
     *
     * @param $date
     * @return bool
     */
    public function validateDate($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') == $date;
    }

    /**
     * check if a datetime is valid
     *
     * @param $datetime
     * @return bool
     */
    public function validateDateTime($datetime)
    {
        $dt = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
        return $dt && $dt->format('Y-m-d H:i:s') == $datetime;
    }
}
