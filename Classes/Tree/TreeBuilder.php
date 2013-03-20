<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Michael Knoll <mimi@kaktusteam.de>
*  			Daniel Lienert <daniel@lienert.cc>
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
 * Class implements Tree Builder
 *
 * As creation of trees from nested sets nodes is a
 * complicated task, we separate creation logic from tree logic
 * within this class.
 *
 * @package Tree
 * @author Michael Knoll <mimi@kaktusteam.de>
 * @author Daniel Lienert <daniel@lienert.cc>
 * @author Joachim Mathes <joachim_mathes@web.de>
 */
class Tx_PtExtbase_Tree_TreeBuilder implements Tx_PtExtbase_Tree_TreeBuilderInterface {

	/**
	 * Holds an instance of node repository
	 *
	 * @var Tx_PtExtbase_Tree_NodeRepositoryInterface
	 */
	protected $nodeRepository;



    /**
     * If set to true, restricted depth will be respected when building the tree
     *
     * @var bool
     */
    protected $respectRestrictedDepth;



    /**
     * If set to a value > 0, tree will only be build up to given level.
     *
     * Level -1 = all levels are build
     * Level 1 = means, only root node will be build
     * Level 2 = root node and its children are build
     * ...
     *
     * @var int
     */
    protected $restrictedDepth;


	
	/**
	 * @var array
	 */
	protected $treeCache;




	/**
	 * Constructor for treebuilder. Requires node repository as parameter.
	 *
	 * @param Tx_PtExtbase_Tree_NodeRepositoryInterface $nodeRepository
	 */
	public function __construct(Tx_PtExtbase_Tree_NodeRepositoryInterface $nodeRepository) {
		$this->nodeRepository = $nodeRepository;
	}



    /**
     * Returns an empty tree with root node labeled by given label
     *
     * @param string $namespace Namespace for tree
     * @param string $rootLabel Label for root node
     * @return Tx_PtExtbase_Tree_Tree Empty tree object.
     */
    public function getEmptyTree($namespace, $rootLabel = '') {
        return Tx_PtExtbase_Tree_Tree::getEmptyTree($namespace, $rootLabel);
    }



	/**
	 * @param string $namespace
	 * @return Tx_PtExtbase_Tree_Tree
	 */
	public function buildTreeForNamespaceWithoutInaccessibleSubtrees($namespace) {
		if (empty($this->treeCache[$namespace])) {
			$this->treeCache[$namespace] = $this->buildTreeForNamespace($namespace);
		}

		$tree = $this->treeCache[$namespace];
		$root = $tree->getRoot();
		if ($root->isAccessible()) {
			$clonedRoot = $this->getClonedNode($root);
			$this->buildAccessRestrictedTreeRecursively($root, $clonedRoot);
		}

		$clonedTree = NULL;
		$clonedTree = Tx_PtExtbase_Tree_Tree::getInstanceByRootNode($clonedRoot);
		$clonedTree->setRestrictedDepth($this->restrictedDepth);
		$clonedTree->setRespectRestrictedDepth($this->respectRestrictedDepth);

		return $clonedTree;
	}



	/**
	 * @param Tx_PtExtbase_Tree_Node $originalNode
	 * @param Tx_PtExtbase_Tree_Node $clonedNode
	 */
	protected function buildAccessRestrictedTreeRecursively($originalNode, $clonedNode) {
		foreach ($originalNode->getChildren() as $child) {
			if ($child->isAccessible()) {
				$clonedChild = $this->getClonedNode($child);
				$clonedChild->setParent($clonedNode);
				$this->buildAccessRestrictedTreeRecursively($child, $clonedChild);
			}
		}
	}



	/**
	 * @param Tx_PtExtbase_Tree_Node $node
	 * @return Tx_PtExtbase_Tree_Node
	 */
	protected function getClonedNode(Tx_PtExtbase_Tree_Node $node) {
		$clonedNode = clone $node;
		$clonedNode->clearRelatives();
		return $clonedNode;
	}



	/**
	 * Builds a tree for given namespace.
	 *
	 * If there are no nodes for given namespace, a new, empty tree with a single root node will be returned.
	 *
	 * @param $namespace
	 * @param $respectEnableFields boolean
	 * @return Tx_PtExtbase_Tree_Tree
	 * @throws Exception
	 */
	public function buildTreeForNamespace($namespace, $respectEnableFields = TRUE) {
		/**
		 * Explanation: We build the tree bottom-up and therefore use a stack.
		 * Each node is added to a child to topStack, if topStack's right-value is smaller
		 * than current node's right-value.
		 */
		$this->nodeRepository->setRespectEnableFields($respectEnableFields);

		$nodes = $this->nodeRepository->findByNamespace($namespace);

		// We have no nodes for given namespace, so we return empty tree with single root node
		if ($nodes->count() == 0) {
			return $this->getEmptyTree($namespace);
		}

		$stack = new Tx_PtExtbase_Tree_Stack();
		$prevLft = PHP_INT_MAX;

		foreach ($nodes as $node) {
			/* @var $node Tx_PtExtbase_Tree_Node */
			/* Assertion: Nodes must be given in descending left-value order. */
			if ($node->getLft() > $prevLft) throw new Exception('Nodes must be given in descending left-value order', 1307861852);

			$prevLft = $node->getLft();
			#echo "<br><br>Knoten: " . $node->toString();

			if ($stack->isEmpty() || $stack->top()->getRgt() > $node->getRgt()) {
				$stack->push($node);
				#echo "Pushed on stack:" . $stack->toString();
			} else {
				#echo "Adding children:";
				while (!$stack->isEmpty() && $stack->top()->getRgt() < $node->getRgt()) {
					#echo "In while - current node " . $node->toString() . " current topStack: " . $stack->top()->toString();
					$stack->top()->setParent($node, false);
					$node->addChild($stack->top(), false);
					$stack->pop();
					#echo "After while-iteration: ". $stack->toString();
				}
				$stack->push($node);
				#echo "After pushing after while: <ul>" . $stack->toString() . "</ul>";
			}
		}

		$tree = Tx_PtExtbase_Tree_Tree::getInstanceByRootNode($stack->top());

		$tree->setRestrictedDepth($this->restrictedDepth);
		$tree->setRespectRestrictedDepth($this->respectRestrictedDepth);

		#echo "Finished tree: " . $tree->toString();
		return $tree;
	}



    /**
     * Setter for restricted depth.
     *
     * If depth is restricted, tree is build only to given level by tree builder.
     *
     * @param int $restrictedDepth
     */
    public function setRestrictedDepth($restrictedDepth) {
        $this->restrictedDepth = $restrictedDepth;
    }




    /**
     * Sets respect restricted depth to given value.
     *
     * If set to true, tree builder will respect restricted depth, when building tree.
     *
     * @param bool $respectRestrictedDepth
     */
    public function setRespectRestrictedDepth($respectRestrictedDepth = TRUE) {
        $this->respectRestrictedDepth = $respectRestrictedDepth;
    }



	/**
	 * @param boolean $respectEnableFields
	 */
	public function setRespectEnableFields($respectEnableFields) {
		$this->nodeRepository->setRespectEnableFields($respectEnableFields);
	}

}
?>