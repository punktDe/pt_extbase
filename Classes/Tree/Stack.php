<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Michael Knoll <mimi@kaktusteam.de>
*           Daniel Lienert <daniel@lienert.cc>
*           
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Class implements a stack
 * 
 * push($element) will push an element on the stack
 * pop() will remove top element from stack and return stack
 * top() will return top element of stack without popping it
 * isEmpty() returns true, if stack is empty
 *
 * @package Tree
 * @author Michael Knoll <mimi@kaktusteam.de>
 * @author Joachim Mathes <mathes@punkt.de>
 * @author Daniel Lienert <daniel@lienert.cc>
 */
class Tx_PtExtbase_Tree_Stack {

	/**
	 * Holds an elements array for stack
	 *
	 * @var array
	 */
	protected $elements;
	
	
	
	/**
	 * Constructor returns empty stack
	 */
	public function __construct() {
		$this->elements = array();
	}
	
	
	
	/**
	 * Returns top element of stack
	 *
	 * @return mixed Top element of stack
	 */
	public function top() {
		if ($this->isEmpty()) 
		    throw new Exception('Trying to get top element of empty stack!', 1307861850);
		return end($this->elements);
	}
	
	
	
	/**
	 * Pushes an element upon the stack
	 *
	 * @param mixed $element
	 */
	public function push($element) {
		$this->elements[] = $element;
	}
	
	
	
	/**
	 * Pops element from stack and returns popped stack
	 *
	 * @return Tx_PtExtbase_Tree_Stack
	 */
	public function pop() {
		if ($this->isEmpty())
		    throw new Exception('Trying to pop an empty stack!', 1307861851);
		array_pop($this->elements);
		return $this;
	}
	
	
	
	/**
	 * Returns true, if stack is empty
	 *
	 * @return bool Returns true, if stack is empty
	 */
	public function isEmpty() {
		return empty($this->elements);
	}
	
	

    /**
     * Returns a string representation of this stack
     *
     * @return string
     */
	public function toString() {
		$string = '';
		foreach (array_reverse($this->elements) as $node) {
			$string .= $node->toString();
		}
		return $string;
	}
	
}
?>