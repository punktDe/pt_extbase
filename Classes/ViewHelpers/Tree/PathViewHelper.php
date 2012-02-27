<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011-2012 punkt.de GmbH <extensions@punkt.de>
*  Authors: Daniel Lienert
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
 * View helper renders the path of a category
 *
 * @author Daniel Lienert
 * @package pt_certification
 * @subpackage ViewHelpers\Category
 */
class Tx_PtExtbase_ViewHelpers_Tree_PathViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var <array>Tx_PtExtbase_Tree_Tree array of treeInstances by namespace
	 */
	protected static $categoryTreeInstances = array();


	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('repository', 'string', 'Specifies the node repository', TRUE);
		$this->registerArgument('namespace', 'string', 'Specifies the tree namespace', TRUE);
		$this->registerArgument('node', 'integer', 'The node uid', TRUE);
		$this->registerArgument('skipRoot', 'boolean', 'Skip the root node', FALSE, FALSE);
	}


	/**
	 * Checks, if the given frontend user has access
	 *
	 * @return string The output
	 */
	public function render() {
		$tree = $this->getTree();
		$nodes = $this->getPathFromRootToNode($tree);
		$firstNode = TRUE;

		if(!$nodes) {
			$result = 'The node with the id ' . $this->arguments['node'] . ' could not be found in the given tree.';
		} else {

			$result = '';

			foreach($nodes as $node) {
				$this->templateVariableContainer->add('node', $node);
				$this->templateVariableContainer->add('firstNode', $firstNode);

				$result .= $this->renderChildren();

				$this->templateVariableContainer->remove('node');
				$this->templateVariableContainer->remove('firstNode');

				$firstNode = FALSE;
			}
		}

		return $result;
	}



	/**
	 * @param $tree Tx_PtExtbase_Tree_Tree
	 * @return array nodes in the path from root to node
	 */
	protected function getPathFromRootToNode(Tx_PtExtbase_Tree_Tree $tree) {
		$pathFromNodeToRoot = array();
		$node = $tree->getNodeByUid($this->arguments['node']);

		if($node instanceof Tx_PtExtbase_Tree_Node) {

			$pathFromNodeToRoot[] = $node;

			while($node != $tree->getRoot()) {
				$node = $node->getParent();
				$pathFromNodeToRoot[] = $node;
			}

			if($this->arguments['skipRoot'] == TRUE) {
				array_pop($pathFromNodeToRoot);
			}

			return array_reverse($pathFromNodeToRoot);

		} else {
			return null;
		}
	}


	/**
	 * @return Tx_PtExtbase_Tree_Tree
	 */
	protected function getTree() {

		$nameSpace = $this->arguments['namespace'];
		$repository = $this->arguments['repository'];

		if(!self::$categoryTreeInstances[$repository.$nameSpace]) {

			$treeRepositoryBuilder = Tx_PtExtbase_Tree_TreeRepositoryBuilder::getInstance();
			$treeRepositoryBuilder->setNodeRepositoryClassName($repository);
			$treeRepository = $treeRepositoryBuilder->buildTreeRepository();

			$tree = $treeRepository->loadTreeByNamespace($nameSpace);

			self::$categoryTreeInstances[$repository.$nameSpace] = $tree;
		}

		return self::$categoryTreeInstances[$repository.$nameSpace];
	}

}
?>
