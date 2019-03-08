<?php

namespace Domino;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This class represents a domino game between players - singleton.
 */
class Game
{

    /* The game status, the game will run until this is set to false again. */
    private static $gameStatus = false;

    /* List of players that attend the game - to switch between them, etc. */
    private $playerList;

    /* How many cards are dealed to players in the beginning, always 7 in our case. */
    private $tilesPerHand;

    /**
     * Bards and historians will want to remember this great game.
     * Let's save some logs for them!
     */
    private $logs;

    /**
     * Let's start it.
     *
     * @param none
     * @return Game
     */
    public static function init() : Game
    {
        if (!self::$gameStatus) {
            self::$gameStatus = new Game();
        }
        
        return self::$gameStatus;
    }

    /**
     * Private constructor, we want to initialize this once.
     */
    private function __construct()
    {

        /* Now harcoded to '7', this could change depending on domino variants. */
        $this->tilesPerHand = 7;

        /**
         * Vectors: playerList and logs
         *
         * Strengths:
         * - very low memory usage
         * - get, set, push and pop are O(1)
         *
         * Weaknesses:
         * - insert, remove, shift, and unshift are O(n).
         *
         * Player starts with an empty hand.
         */
        $this->playerList = new \Ds\Vector();

        $this->logs = new \Ds\Vector();
    }

    /**
     * Starts the game: init classes, deal domino tiles, etc.
     *
     * Alea iacta est!
     *
     * @param array $names The user names we passed as arguments
     * @return void
     */
    public function start(array $names) : void
    {
        /* Initializes & randomizes domino pack */
        $pack = new Pack();
        $pack->randomize();

        /* Creates board & adds introductory log data */
        $board = new Board();
        $this->introCli();

        /* Creates players and assigns hands */
        $this->createPlayers($names, $pack);

        /* Choose a random member and card to start the fight. */
        $startTile = $this->randomizeStart($board);

        /* Runs the loop until the game ends. */
        $this->runGame($board, $pack);
    }

    /**
     * Creates players and deals hands.
     *
     * @param array $names
     * @param Pack $pack
     * @return void
     */
    private function createPlayers(array $names, Pack $pack) : void 
    {
        foreach ($names as $name) {
            $player = new Player($name);
            $this->addPlayer($player); // adds the player to the gaming list
            $hand = $pack->dealHand($player); // assigns 7 dominoes to each player
            $this->logs->push("<info>👤 {$name} gets the following tiles: {$hand}</info>");
        }
    }

    /**
     * Retrieve game history.
     *
     * @param none
     * @return \Ds\Vector
     */
    public function history() : \Ds\Vector
    {
        return $this->logs;
    }

    /**
     * Adds an individual player to the player list of this game.
     *
     * @param Player $player
     * @return void
     */
    private function addPlayer(Player $player) : void
    {
        $this->playerList->push($player);
    }
  
    /**
     * Start game info - prints some general details about the current match.
     *
     * @param none
     * @return void
     */
    private function introCli() : void
    {
        $this->logs->push('<info> - Creating domino pack...</info>');
        $this->logs->push('<info> - Shuffling domino pack...</info>');
        $this->logs->push('<info> - Creating players...</info>');
        $this->logs->push('<info> - Creating board...</info>');
        $this->logs->push('<info> - Randomizing start player & domino...</info>'.PHP_EOL);
        $this->logs->push('Players are now fighting in auto-mode - may the best bot win!'.PHP_EOL);
    }

    /**
     * We only have two players, so we can switch by rotating the vector by '1' each time.
     * Then we'll pick the first person in the list.
     *
     * @param none
     * @return Player
     */
    private function switchPlayer() : Player
    {
        $this->playerList->rotate(1);
        return $this->playerList->first();
    }

    /**
     * This loops until one player finishes all dominoes or the domino pack is empty.
     *
     * @param Board $board
     * @param Pack $pack
     * @return void
     */
    private function runGame(Board $board, Pack $pack) : void
    {
        $num = 1; // to store game rounds

        while (self::$gameStatus):

            $num++;

            /* Switches the player, for each move */
            $thisPlayer = $this->switchPlayer();

            /* More CLI info & logs... for those bards we mentioned before. */
            $this->logs->push(PHP_EOL."<question>Round {$num}: {$thisPlayer->print()}</question>");
            $this->logs->push("<comment>Board is now: {$board->print()}</comment>");
            $this->logs->push("<info>Tiles in hand: {$thisPlayer->printHand()}</info>");
                    
            /* Retrieve tiles that can be played in the current hand */
            $goodTiles = $thisPlayer->playableTiles($board->getSides());

            /**
             * If we have any tiles that the player can use, we play them.
             * Otherwise we draw a new one from the Pack.
             */
            if ($goodTiles->count() > 0) {
                /**
                 * The simplest implementation is to retrieve the first playable tile.
                 * A more sophisticated (and realistic) approach should compute which tile would bring a better chance
                 * of success for the player's final victory.
                 */
                $selected = $goodTiles->first();

                $playableNow = $goodTiles->map(function ($value) {
                    return $value->print();
                })->join(" ");

                /* Shows domino tiles that can be played right away */
                $this->logs->push("⭐️ Playable: {$playableNow}");
                        
                /* Remove domino from the player's hand */
                $thisPlayer->removeTile($selected);

                /**
                 * Place domino on the board, assigning it based on compatibilty to the beginning or the end of the queue.
                 */
                $linked = $board->matchTile($selected);
                        
                /* And yet another log telling us about how this player rocks. */
                $this->logs->push("👤 {$thisPlayer->print()} plays {$selected->print()} to connect with <comment>{$linked->print()}</comment>");
            } else {
                /* The player can't play any domino, so he (or she!) needs to draw another tile */
                $this->logs->push("👤 {$thisPlayer->name} can't play - drawing new domino tile.");
                $pack->servesPlayer($thisPlayer);
            }

            /* The pack size */
            $this->logs->push("📐 Pack size: {$pack->size()}");

            /* Breaks the loop when the game ends */
            $this->checkGameEnd($thisPlayer, $pack, $num);

        endwhile;
    }

    /**
     * This method will break the loop when one of the following conditions is met:
     * - player has no more tiles in hand
     * - no more tiles are available in the pack to draw
     *
     * @param Player $player
     * @param Pack $pack
     * @param int $num
     * @return void
     */
    private function checkGameEnd(Player $player, Pack $pack, int $num) : void
    {
        if ($player->handSize() === 0) {
            /* A player finishes all dominoes - hooray for the winner! */
            self::$gameStatus = false;
            $this->logs->push(PHP_EOL."<error>{$player->print()} wins in {$num} moves!</error>");

        } elseif ($pack->size() === 0) {
            /* There are no more dominoes in the pack */
            self::$gameStatus = false;
            $this->logs->push(PHP_EOL."<error>Game ends with no winner:</error>");
            $this->playerList->map(function ($value) {
                $this->logs->push("<info>{$value->print()} has {$value->handSize()} dominoes left.</info>");
            });

        }
    }

    /**
     * Pick a random user and tile to start the game.
     *
     * @param Board $board
     * @return Tile
     */
    private function randomizeStart(Board $board) : Tile
    {
        /* Select a random player and place it at the start of the player list */
        $rPlayer = mt_rand(0, $this->playerList->count() - 1);
        $player = $this->playerList->remove($rPlayer);
        $this->playerList->unshift($player);
        
        /* Select random domino tile */
        $rTile = mt_rand(0, $this->tilesPerHand - 1);
        $tile = $player->removeTilebyID($rTile);

        /* Add first domino to the board */
        $board->addLeft($tile);

        /* Log first player and tile */
        $this->logs->push(PHP_EOL."<error>Round #1: {$player->print()} starts the game with {$tile->print()}</error>");

        return $tile;
    }
}
