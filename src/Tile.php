<?php

namespace Domino;

/**
 * This class represents a single domino tile.
 */
class Tile
{

  /* 1st (top or in our case left) half of the tile */
    public $half1;

    /* 2nd (bottom or in our case right) half of the tile */
    public $half2;

    /* Is the tile flipped? */
    public $isFlipped;

    /** 
     * @param int $half1 Represents the 1st half of a domino tile
     * @param int $half2 Represents the 2nd half of a domino tile
     * @param bool $isFlipped Used to check if the tile has been flipped
     */
    public function __construct(int $half1, int $half2, bool $isFlipped)
    {
        $this->half1 = $half1;
        $this->half2 = $half2;
        $this->isFlipped = $isFlipped;
    }

    /**
     * Tile getter.
     *
     * @param none
     * @return array
     */
    public function get() : array
    {
        return [$this->half1, $this->half2, $this->isFlipped];
    }

    /**
     * Tile flipper.
     *
     * @param none
     * @return void
     */
    public function flip() : void
    {
        $this->isFlipped = !$this->isFlipped;

        /** Switches the domino halves */
        $this->half1 = $this->half1 + $this->half2;
        $this->half2 = $this->half1 - $this->half2;
        $this->half1 = $this->half1 - $this->half2;
    }

    /**
     * Gets the flipped status.
     *
     * @param none
     * @return bool Representing the tile's flipped status
     */
    public function isFlipped() : bool
    {
        return $this->isFlipped;
    }

    /**
     * Prints the domino tile values in human-readable format.
     *
     * @param none
     * @return string
     */
    public function print() : string
    {
        return '<' . $this->half1 . ':' . $this->half2 . '>';
    }
}
