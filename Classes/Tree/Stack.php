<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

class Stack
{
    /**
     * Holds an elements array for stack
     *
     * @var array
     */
    protected $elements;
    
    
    
    /**
     * Constructor returns empty stack
     */
    public function __construct()
    {
        $this->elements = [];
    }


    /**
     * @return mixed
     * @throws \Exception
     */
    public function top()
    {
        if ($this->isEmpty()) {
            throw new \Exception('Trying to get top element of empty stack!', 1307861850);
        }
        return end($this->elements);
    }
    
    
    
    /**
     * Pushes an element upon the stack
     *
     * @param mixed $element
     */
    public function push($element)
    {
        $this->elements[] = $element;
    }


    /**
     * @return $this
     * @throws \Exception
     */
    public function pop()
    {
        if ($this->isEmpty()) {
            throw new \Exception('Trying to pop an empty stack!', 1307861851);
        }
        array_pop($this->elements);
        return $this;
    }
    
    
    
    /**
     * Returns true, if stack is empty
     *
     * @return bool Returns true, if stack is empty
     */
    public function isEmpty()
    {
        return empty($this->elements);
    }
    
    

    /**
     * Returns a string representation of this stack
     *
     * @return string
     */
    public function toString()
    {
        $string = '';
        foreach (array_reverse($this->elements) as $node) {
            $string .= $node->toString();
        }
        return $string;
    }
}
