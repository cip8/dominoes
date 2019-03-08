<?php

namespace Domino;

/**
 * Class for our ambitious player, who will take this game of dominoes to new heights.
 */
class Player
{

    /* The player's name */
    public $name;

    /* The player's hand: all the dominoes he holds at a certain time. */
    private $hand;

    /**
      * Creates a new player with a given name.
      *
      * @param name the name of the player.
      */
    public function __construct(string $name)
    {

        /* Initialize player data fields (currently only the name) */
        $this->name = $name;
  
        /**
         * Players start with an empty hand.
         * For the player's hand we'll use a vector.
         *
         * Strengths:
         * - very low memory usage
         * - get, set, push and pop are O(1)
         *
         * Weaknesses:
         * - insert, remove, shift, and unshift are O(n).
         */
        $this->hand = new \Ds\Vector();
    }

    /**
     * Adds a tile to the player's hand.
     *
     * @param Tile $title The domino to be added to the player's hand
     * @return void
     */
    public function addTile(Tile $tile) : void
    {
        $this->hand->push($tile);
    }

    /**
      * Removes a domino tile from the player's hand and returns the value.
      *
      * @param Tile $title The domino in the hand to be removed
      * @return Tile The domino that is removed from the hand
      */
    public function removeTile(Tile $tile) : Tile
    {
        return $this->hand->remove($this->hand->find($tile));
    }
  
    /**
     * Removes a domino tile (by index) from the player's hand and returns the value.
     *
     * @param int $index The domino's index to be removed
     * @return Tile the domino that is removed from the hand
     */
    public function removeTilebyId(int $index): Tile
    {
        return $this->hand->remove($index);
    }

    /**
      * Determines if a player has a domino that can be played on the board.
      *
      * @param array $ends Contains the Board's extremities
      * @return Vector Tiles that can be played
      */
    public function playableTiles(array $ends) : \Ds\Vector
    {
        return $this->hand->filter(function($tile) use ($ends) { 
            return $this->matchesBoardEnds($tile, $ends); 
        });
    }

    /**
     * Check if a tile matches the board's ends
     * 
     * @param Tile $tile
     * @param array $ends
     */
    private function matchesBoardEnds(Tile $tile, array $ends)
    {
        if (in_array($tile->half1, $ends) || in_array($tile->half2, $ends)) {
            return $tile;
        }
    }

    /**
     * Gets the number of dominos in a player's hand.
     *
     * @param none
     * @return int the number of dominos in the player's hand
     */
    public function handSize() : int
    {
        return $this->hand->count();
    }
  
    /**
     * Gets dominos held by the player.
     *
     * @param none
     * @return Vector list of domino tiles in this player's hand
     */
    public function getHand() : \Ds\Vector
    {
        return $this->hand;
    }

    /**
     * Gets dominos held by the player in human readable format.
     *
     * @param none
     * @return string list of domino tiles in this player's hand, as string
     */
    public function printHand() : string
    {
        return $this->hand->map(function ($value) {
            return '<'. $value->half1 . ':'. $value->half2 .'>';
        })->join(" ");
    }

    /**
     * Gets Player data in human-readable format.
     *
     * @param none
     * @return string Player' name
     */
    public function print() : string
    {
        return $this->name;
    }
}
