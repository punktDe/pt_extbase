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
 * Returns a path or path chunks of the path from a node to the root
 *
 * @package Tree
 * @author Daniel Lienert <lienert@punkt.de>
 */
class Tx_PtExtbase_Tree_NodePathBuilder  {

	/**
	 * @var <array>Tx_PtExtbase_Tree_NodePathBuilder
	 */
	protected static $instances = array();

	/**
	 * @var Tx_PtExtbase_Tree_Tree
	 */
	protected $tree;


	/**
	 * @var array
	 */
	protected $nodePathCache;


	/**
	 * @static
	 * @param $repository
	 * @param $nameSpace
	 * @return Tx_PtExtbase_Tree_NodePathBuilder
	 */
	public static function getInstanceByRepositoryAndNamespace($repository, $nameSpace) {
		if(!self::$instances[$repository.$nameSpace]) {
			$instance = new Tx_PtExtbase_Tree_NodePathBuilder();
			$instance->setTree($instance->buildTree($repository, $nameSpace));
			self::$instances[$repository.$nameSpace] = $instance;
		}

		return self::$instances[$repository.$nameSpace];
	}



	public function __construct(){}



	/**
	 * @param $repository
	 * @param $nameSpace
	 * @return Tx_PtExtbase_Tree_Tree
	 */
	public function buildTree($repository, $nameSpace) {
		$treeRepositoryBuilder = Tx_PtExtbase_Tree_TreeRepositoryBuilder::getInstance();
		$treeRepositoryBuilder->setNodeRepositoryClassName($repository);
		$treeRepository = $treeRepositoryBuilder->buildTreeRepository();

		return $treeRepository->loadTreeByNamespace($nameSpace);
	}



	/**
	 * @param int $nodeUid
	 * @param int $startIndex
	 * @param int $length
	 * @return array nodes as plain array
	 */
	public function getPathFromRootToNode($nodeUid, $startIndex = 0, $length = 1000) {
		$reversedArray = array_reverse($this->buildPathFromNodeToRoot($nodeUid));
		return array_slice($reversedArray, $startIndex, $length);
	}



	/**
	 * @param int $nodeUid
	 * @param int $startIndex
	 * @param int $length
	 * @return array nodes as plain array
	 */
	public function getPathFromNodeToRoot($nodeUid, $startIndex = 0, $length = 1000) {
		$slicedArray = array_slice($this->buildPathFromNodeToRoot($nodeUid), $startIndex, $length);
		return $slicedArray;
	}



	/**
	 * @param int $nodeUid
	 * @return array|null
	 */
	protected function buildPathFromNodeToRoot($nodeUid) {

		if(!array_key_exists($nodeUid, $this->nodePathCache)) {

			$node = $this->tree->getNodeByUid($nodeUid);

			$pathFromNodeToRoot = array();

			if($node instanceof Tx_PtExtbase_Tree_Node) {

				$pathFromNodeToRoot[] = $node;

				while($node != $this->tree->getRoot()) {
					$node = $node->getParent();
					$pathFromNodeToRoot[] = $node;
				}

				$this->nodePathCache[$nodeUid] = $pathFromNodeToRoot;

			} else {
				$this->nodePathCache[$nodeUid] = NULL;
			}
		}

		return $this->nodePathCache[$nodeUid];
	}



	/**
	 * @param \Tx_PtExtbase_Tree_Tree $tree
	 */
	public function setTree($tree) {
		$this->tree = $tree;
	}
}
?>