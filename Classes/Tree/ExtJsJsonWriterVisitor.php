<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Daniel Lienert <daniel@lienert.cc>, Michael Knoll <mimi@kaktusteam.de>
*  All rights reserved
*
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
 * Class implements a visitor for getting an extJs tree compatible array
 *
 * @package Tree
 * @author Daniel Lienert <daniel@lienert.cc>
 */
class Tx_PtExtbase_Tree_ExtJsJsonWriterVisitor implements  Tx_PtExtbase_Tree_TreeWalkerVisitorInterface {


	/**
	 * @var array
	 */
	protected $nodeArray = array();


	/**
	 * Holds stack of unfinished nodes
	 *
	 * @var Tx_PtExtbase_Tree_Stack
	 */
	protected $nodeStack;


	/**
	 * @var array
	 */
	protected $selection;


	/**
	 * @var boolean
	 */
	protected $multipleSelect;


	/**
	 * A callback function to call via call_user_func in doFirstVisit
	 *
	 * @var array(target => className|object, method => method)
	 */
	protected $firstVisitCallback = NULL;


	/**
	 * A callback function to call via call_user_func in doFirstVisit
	 *
	 * @var array(target => className|object, method => method)
	 */
	protected $lastVisitCallback = NULL;


	/**
	 * @var int
	 */
	protected $maxDepth = PHP_INT_MAX;



	/**
	 * Constructor for visitor
	 */
	public function __construct() {
		$this->nodeStack = new Tx_PtExtbase_Tree_Stack();
	}



	/**
	 * @see Tx_PtExtbase_Tree_TreeWalkerVisitorInterface::doFirstVisit()
	 *
	 * @param Tx_PtExtbase_Tree_NodeInterface $node
     * @param int &$index Holds the visitation index of treewalker
     * @param int &$level Holds level of visitation in tree, starting at 1
     */
    public function doFirstVisit(Tx_PtExtbase_Tree_NodeInterface $node, &$index, &$level) {
		$arrayForNode = array(
			'id' => $node->getUid(),
			'text' => $node->getLabel(),
			'children' => array(),
			'leaf' => !$node->hasChildren(),
		);

		$this->setSelectionOnNodeArray($node, $arrayForNode);

		if($this->firstVisitCallback) {
			$return = call_user_func(array($this->firstVisitCallback['target'], $this->firstVisitCallback['method']), $node, $arrayForNode);
			if($return === FALSE) {
				throw new Exception('It was not possible to call '.  get_class($this->firstVisitCallback['target']) . '::' . $this->firstVisitCallback['method'], 1328468070);
			} else {
				$arrayForNode = $return;
			}
		}

		$this->nodeStack->push($arrayForNode);
	}



	/**
	 * @param $node
	 * @param $arrayForNode
	 */
	protected function setSelectionOnNodeArray($node, &$arrayForNode) {
		if($this->multipleSelect) {
			if(is_array($this->selection) && in_array($node->getUid(), $this->selection)) {
				$arrayForNode['checked'] = true;
			} else {
				$arrayForNode['checked'] = false;
			}
		} else {
			if($node->getUid() == (int) $this->selection) {
				$arrayForNode['cls'] = 'selectedNode';
			}
		}
	}



	/**
	 * @see Tx_PtExtbase_Tree_TreeWalkerVisitorInterface::doLastVisit()
	 *
	 * @param Tx_PtExtbase_Tree_NodeInterface $node
     * @param int &$index Holds the visitation index of treewalker
     * @param int &$level Holds level of visitation in tree, starting at 1
     */
    public function doLastVisit(Tx_PtExtbase_Tree_NodeInterface $node, &$index, &$level) {
		$currentNode = $this->nodeStack->top();
		$this->nodeStack->pop();

		if($this->lastVisitCallback) {
			$currentNode = call_user_func(array($this->lastVisitCallback['target'], $this->lastVisitCallback['method']), $node, $currentNode);
		}

		if (!$this->nodeStack->isEmpty()) {
			$parentNode = $this->nodeStack->top();
			$this->nodeStack->pop();
			$parentNode['children'][] = $currentNode;
			$currentNode['leaf'] = 'false';
			$this->nodeStack->push($parentNode);
		} else {
			$this->nodeArray = $currentNode;
		}
	}



	/**
	 * Returns array structure for visited nodes
	 *
	 * @return array
	 */
	public function getNodeArray() {
		return $this->nodeArray;
	}



	/**
	 * @param boolean $multipleSelect
	 */
	public function setMultipleSelect($multipleSelect) {
		$this->multipleSelect = $multipleSelect;
	}



	/**
	 * @param $selection
	 */
	public function setSelection($selection) {
		$this->selection = $selection;
	}

	/**
	 * @param int $maxDepth
	 */
	public function setMaxDepth($maxDepth) {
		$this->maxDepth = $maxDepth;
	}



	/**
	 * @param $target object or className
	 * @param $method
	 */
	public function registerFirstVisitCallback($target, $method) {
		$this->checkCallBack('firstVisitCallBack', $target, $method);

		$this->firstVisitCallback = array(
			'target' => $target,
			'method' => $method
		);
	}


	/**
	 * @param $target object or className
	 * @param $method
	 */
	public function registerLastVisitCallBack($target, $method) {
		$this->checkCallBack('lastVisitCallBack', $target, $method);

		$this->lastVisitCallback = array(
			'target' => $target,
			'method' => $method
		);
	}


	/**
	 * @param $type
	 * @param $target
	 * @param $method
	 */
	protected function checkCallBack($type, $target, $method) {
		if(is_object($target)) {
			if (!method_exists($target, $method) || !is_callable(array($target, $method))) {
				throw new Exception('The method ' . $method . ' is not accessible on object of type ' . get_class($target) . ' for use as ' . $type, 1328462239);
			}
		} else {
			if(!class_exists($target)) {
				throw new Exception('The class ' . $target . ' could not be found for use as ' . $type, 1328462359);
			}
			if (!method_exists($target, $method)) {
				throw new Exception('The method ' . $method . ' is not accessible in class ' . $target . ' for use as ' . $type, 1328462244);
			}
		}
	}

}
?>