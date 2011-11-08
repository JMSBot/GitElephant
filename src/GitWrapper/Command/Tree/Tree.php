<?php

/*
 * This file is part of the GitWrapper package.
 *
 * (c) Matteo Giachino <matteog@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Just for fun...
 */

namespace GitWrapper\Command\Tree;

use GitWrapper\Command\BaseCommand;
use GitWrapper\Command\Tree\Node;
use GitWrapper\GitBinary;


/**
 * Tree
 *
 * Wrapper for the tree handler commands
 *
 * @author Matteo Giachino <matteog@gmail.com>
 */

class Tree extends BaseCommand implements \ArrayAccess, \Iterator, \Countable
{
    private $children = array();
    private $position = 0;

    public function __construct(GitBinary $binary)
    {
        $this->position = 0;
        $this->binary = $binary;
    }

    public function lsTree($subject)
    {
        $this->addCommandName('ls-tree');
        //$this->addArgument('--name-only');
        $this->addSubject($subject);
    }

    public function execute($repositoryPath, $stdIn = null)
    {
        parent::execute($repositoryPath, $stdIn);
        while(!feof($this->getStdOut())) {
            $this[] = new Node(fgets($this->getStdOut()));
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->children[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->children[$offset]) ? $this->children[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->children[] = $value;
        } else {
            $this->children[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->children[$offset]);
    }

    public function current()
    {
        return $this->children[$this->position];
    }

    public function next()
    {
        ++$this->position;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return isset($this->children[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function count()
    {
        return count($this->children);
    }


}