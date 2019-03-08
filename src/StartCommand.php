<?php
namespace Domino;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Start command for the dominoes game.
 */
class StartCommand extends SymfonyCommand
{
  
    /**
     * Configure the command.
     */
    public function configure()
    {
        $this->setName('start')
            ->setDescription('<comment>Starts the dominoes game</comment>')
            ->addArgument(
                'name',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                '<comment>Enter player name [example: ./domino start "Alice" "Bob"]</comment>.'
            );
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output) : void
    {
    
        /* Get the player names */
        $names = $input->getArgument('name');

        /**
         * Game currenlty designed for two players
         * Ask for exaclty two names or throw an error.
         */
        if (count($names) !== 2) {
            $output->writeln('<error>👫 Two players are needed to play!</error>');
            exit(1);
        } else {
            if ($game = Game::init()) {
                /* Let's inform our players that the game will start momentarily. */
                $output->writeln('<info>🎲 Starting game...</info>');

                /* Start the game. */
                $game->start($names);

                /** Echo the game history in the terminal */
                $output->writeln($game->history());
            } else {
                $output->writeln('<info>🙁 A game is currently on - please wait your turn.</info>');
                exit(1);
            }
        }
    }
}
