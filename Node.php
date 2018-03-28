<?php

/**
 * Created by PhpStorm.
 * User: Serhii
 * Date: 24.03.2018
 * Time: 23:34
 */
class Node
{
    private $left;
    private $right;
    private $parent;
    private $word;
    private $distance;
    private $strLength;

    public function __construct(string $word, float $distance, float $strLength)
    {
        $this->distance = $distance;
        $this->strLength = $strLength;
        $this->word = $word;
    }

    /**
     * @return float
     */
    public function getDistance(): float
    {
        return $this->distance;
    }

    /**
     * @return float
     */
    public function getStrLength(): float
    {
        return $this->strLength;
    }

    /**
     * @return string
     */
    public function getWord(): string
    {
        return $this->word;
    }

    /**
     * @param mixed $parent
     */
    public function setParent(Node $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @param mixed $left
     */
    public function setLeft(Node $left)
    {
        $this->left = $left;
    }

    /**
     * @param mixed $right
     */
    public function setRight(Node $right)
    {
        $this->right = $right;
    }

    /**
     * @return mixed
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @return mixed
     */
    public function getRight()
    {
        return $this->right;
    }
}