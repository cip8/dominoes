<?php

namespace Domino;

/**
 * The dominoes pack.
 */
class Pack
{

    /* The dominoes pack. */
    private $pack;

    /**
     * Default constructor creates a pack with 28 tiles.
     * From <0:0> to <6:6>.
     *
     * @param none
     */
    public function __construct()
    {
        $this->pack = [];

        /* Create the 28 dominoes / O(i*j) */
        for ($i = 0; $i <= 6; $i++) {
            for ($j = $i; $j <= 6; $j++) {
                /* Create and set the domino tile */
                $tile = new Tile($i, $j, false);
                /* Update the pack with the newly created tile */
                array_push($this->pack, $tile);
            }
        }
    }

    /**
     * Randomize the domino pack.
     *
     * @param none
     * @return void
     */
    public function randomize() : Pack
    {

        /**
         * A simple shuffle is enough for us.
         * For real-world scenarios a more robust random-generating solution should be used.
         */
        shuffle($this->pack);

        /* Chain other methods after randomizing pack (i.e. $pack->randomize()->get()) */
        return $this;
    }

    /**
     * Getter - retrieve the size of the pack.
     * If the size reaches zero and no player has managed to finish their tiles, the game ends.
     *
     * @param none
     * @return int
     */
    public function size() : int
    {
        return count($this->pack);
    }

    /**
     * Removes a given number of tiles from the pack, and assigns them to a specific player's hand.
     *
     * @param Player $player The player to whom the tiles should be dealt to
     * @param int $cardsPerHand The number of tiles to be dealt
     * @return string Returns the player's hand in human-readable format
     */
    public function dealHand(Player $player, int $cardsPerHand = 7) : string
    {
        for ($i = 0; $i < $cardsPerHand; $i++) {
            $this->servesPlayer($player);
        }

        return $player->printHand();
    }

    /**
     * Puts a new domino tile in the given player's hand.
     *
     * @param Player $player the player to whom the tile should be dealt to
     * @return void
     */
    public function servesPlayer(Player $player) : void
    {
        $player->addTile($this->dealTile());
    }

    /**
     * Takes the top domino from the top of the pack and deals it to players.
     *
     * @param none
     * @return Tile
     */
    public function dealTile() : Tile
    {
        return array_pop($this->pack);
    }
  
    /**
     * Getter - retrieves the dominoes found in a pack
     *
     * @param none
     * @return array of Tiles
     */
    public function get() : array
    {
        return $this->pack;
    }
}
