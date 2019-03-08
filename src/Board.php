<?php

namespace Domino;

/**
 * The game board. Players will try to add their domino tiles here.
 */
class Board
{

    /** Holds the tiles placed on the 'table' by players. */
    public $tiles;

    /** Holds the board extremities, so we can easily check our player's tiles */
    public $sides;

    /**
     * Creates an empty board.
     */
    public function __construct()
    {

        /**
         * Deque
         *
         * Strengths:
         * - low memory usage
         * - get, set, push, pop, shift, and unshift are all O(1)
         *
         * Weaknesses:
         * - insert, remove are O(n)
         * - buffer capacity must be a power of 2.
         *
         * A double-ended queue will store all the domino tiles placed on the table.
         * In principle, this data structure would be a perfect match for our case as tiles can be added to both ends of the board.
         */
        $this->tiles = new \Ds\Deque();

        /* Regular array for board extremities */
        $this->sides = [];
    }

    /**
     * Adds a tile to board, flips it if necessary.
     * P.S.: We already know that the player has a tile which will fit the board.
     *
     * @param Tile $title
     * @return mixed
     */
    public function matchTile(Tile $tile) : Tile
    {
        if ($tile->half1 === $this->sides[1]) {
            $linked = $this->tiles->last();
            $this->addRight($tile);
            return $linked;
        } elseif ($tile->half2 === $this->sides[0]) {
            $linked = $this->tiles->first();
            $this->addLeft($tile);
            return $linked;
        } elseif (!$tile->isFlipped()) {
            $tile->flip();
            return $this->matchTile($tile);
        }
    }
    
    /**
     * Adds a tile to left of the board.
     *
     * @param Tile $title
     * @return void
     */
    public function addLeft(Tile $tile) : void
    {
        $this->tiles->unshift($tile);
        $this->setSides();
    }
    
    /**
     * Adds a tile to right of the board.
     *
     * @param Tile $title
     * @return void
     */
    public function addRight(Tile $tile) : void
    {
        $this->tiles->push($tile);
        $this->setSides();
    }

    /**
     * Sets board extremities.
     *
     * @param none
     * @return void
     */
    private function setSides() : void
    {
        $this->sides = [$this->tiles->first()->half1, $this->tiles->last()->half2];
    }
    
    /**
     * Get the data for all dominoes stored on the board.
     * @param none
     * @return deque
     */
    public function getTiles() : \Ds\Deque
    {
        return $this->tiles;
    }

    /**
     * Gets board extremities.
     *
     * @param none
     * @return void
     */
    public function getSides() : array
    {
        return $this->sides;
    }

    /**
     * Human readable format for domino tiles on this board.
     *
     * @param none
     * @return void
     */
    public function print() : string
    {
        return $this->tiles->map(function ($value) {
            return '<'. $value->half1 . ':'. $value->half2 .'>';
        })->join(" ");
    }
}
