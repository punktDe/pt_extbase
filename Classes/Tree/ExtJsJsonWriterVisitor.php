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
	 * @var Tx_Extbase_SignalSlot_Dispatcher
	 */
	protected $signalSlotDispatcher;


	/**
	 * Constructor for visitor
	 */
	public function __construct() {
		$this->nodeStack = new Tx_PtExtbase_Tree_Stack();
		$this->signalSlotDispatcher = t3lib_div::makeInstance('Tx_Extbase_Object_Manager')->get('Tx_PtExtbase_SignalSlot_Dispatcher');
	}



	/**
	 * @see Tx_PtExtbase_Tree_TreeWalkerVisitorInterface::doFirstVisit()
	 *
	 * @param Tx_PtExtbase_Tree_NodeInterface $node
	 * @param int &$index Visitation index of treewalker
	 */
	public function doFirstVisit(Tx_PtExtbase_Tree_NodeInterface $node, &$index) {
		$arrayForNode = array(
			'id' => $node->getUid(),
			'text' => $node->getLabel(),
			'children' => array(),
			'leaf' => !$node->hasChildren(),
		);

		$this->setSelectionOnNodeArray($node, $arrayForNode);
		if($this->firstVisitCallback) {
			call_user_func(array($this->firstVisitCallback['target'], $this->firstVisitCallback['method']), $node, $arrayForNode);
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
	 * @param int &$index Visitation index of treewalker
	 */
	public function doLastVisit(Tx_PtExtbase_Tree_NodeInterface $node, &$index) {
		$currentNode = $this->nodeStack->top();
		$this->nodeStack->pop();

		if($this->lastVisitCallback) {
			call_user_func(array($this->lastVisitCallback['target'], $this->lastVisitCallback['method']), $node, $currentNode);
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
			if (!method_exists($target, $method)) {
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