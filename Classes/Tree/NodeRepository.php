<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Michael Knoll <mimi@kaktusteam.de>
*           Daniel Lienert <daniel@lienert.cc>
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
 * Repository for Tx_PtExtbase_Tree_Node
 *
 * @package Tree
 * @author Michael Knoll <mimi@kaktusteam.de>
 */
class Tx_PtExtbase_Tree_NodeRepository
    extends Tx_Extbase_Persistence_Repository
    implements Tx_PtExtbase_Tree_NodeRepositoryInterface {
	
	/**
	 * Returns a set of categories determined by the root of the given node.
	 *
     * TODO rename: we do not find by nodeUid but by node object
     *
	 * @param Tx_PtExtbase_Tree_NodeInterface $category
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_PtExtbase_Tree_Node>
	 */
	public function findByRootOfGivenNodeUid(Tx_PtExtbase_Tree_NodeInterface $category) {
		$rootUid = $category->getRoot();
		return $this->findByRootUid($rootUid);
	}



    /**
     * Updates a given node if it has already been added to repository or adds it.
     *
     * @param Tx_PtExtbase_Tree_NodeInterface $node
     */
    public function updateOrAdd(Tx_PtExtbase_Tree_NodeInterface $node) {
        if ($node->getUid() === null || $node->getUid() < 0) {
            // UID of node < 0 means, node has not yet been persisted!
            $this->add($node);
        } else {
            // UID of node > 0 means, node has been persisted before!
            $this->update($node);
        }
    }

	
	
	/**
	 * Returns a set of categories determined by uid of root node
	 *
	 * @param int $rootUid
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_PtExtbase_Tree_Node>
	 */
	public function findByRootUid($rootUid) {
		$query = $this->createQuery();
        $query->matching($query->equals('root', $rootUid))
            ->setOrderings(array('lft' => Tx_Extbase_Persistence_Query::ORDER_DESCENDING));
        return $query->execute();
	}



    /**
     * Returns set of nodes for given namespace.
     *
     * @param $namespace
     * @return Tx_Extbase_Persistence_ObjectStorage<Tx_PtExtbase_Tree_Node>
     */
    public function findByNamespace($namespace) {
        $query = $this->createQuery();
        $query->matching($query->equals('namespace', $namespace))
            ->setOrderings(array('lft' => Tx_Extbase_Persistence_Query::ORDER_DESCENDING));
        return $query->execute();
    }
	
	
	
	/**
	 * Removes a category and its subcategories
     *
     * TODO as long as we only operate on trees, we don't need this. This is only required if we remove a single node out of tree-scope
	 *
	 * @param Tx_PtExtbase_Tree_Node $category Category to be removed
	 */
	public function remove($category) {

		/*
		 * WARNING Whenever this goes productive, we have to make sure,
		 * that the category table is write locked, while we
		 * do the following three queries!
		 */

		// We delete all database records that are no longer required
		$this->deleteCategory($category);

		// We update object structure
		if (!$category->isRoot()) {
			/**
			 * What happens here:
			 *
			 * If we delete a node from the tree, there will be a "gap" in the lft - rgt numbers. We "fill" this gap by
			 * subtracting the difference (rgt - lft + 1)
			 * 1. from nodes rgt & lft number if node has a bigger lft number than deleted node
			 * 2. from nodes rgt number, if node has a smaller lft and a bigger rgt number than deleted node
			 * then the node we want to delete.
			 * Afterwards, everything is fine again.
			 */
			$left = $category->getLft();
			$right = $category->getRgt();
			$difference = intval($right - $left + 1);

			// We update case 1. from above
			$query1 = "UPDATE tx_ptextbase_tree_node " .
			          "SET lft = lft - " . $difference . ", rgt = rgt - " . $difference . " " .
                      "WHERE namespace = \"" . $category->getNamespace() . "\" " .
			          "AND lft > " . $category->getLft();
			#echo "Update 1: " . $query1;
            $extQuery1 = $this->createQuery();
            $extQuery1->getQuerySettings()->setReturnRawQueryResult(true); // Extbase WTF
            $extQuery1->statement($query1)->execute();

			// We update case 2. from above
			$query2 = "UPDATE tx_ptextbase_tree_node " .
			          "SET rgt = rgt - " . $difference . " " .
			          "WHERE namespace = \"" . $category->getNamespace() . "\" " .
			          "AND lft < " . $category->getLft() . " " .
			          "AND rgt > " . $category->getRgt();
			#echo "Update 2: " . $query2;
            $extQuery2 = $this->createQuery();
            $extQuery2->getQuerySettings()->setReturnRawQueryResult(true); // Extbase WTF
            $extQuery2->statement($query2)->execute();
		}
	}
	
	
	
	/**
	 * Hard-deletes a category and its subcategories from database.
	 * No deleted=1 is set, categories are really deleted!
	 *
	 * @param Tx_PtExtbase_Tree_Node $category
	 */
	protected function deleteCategory(Tx_PtExtbase_Tree_Node $category) {
        $left = $category->getLft();
        $right = $category->getRgt();
        
        $query = "DELETE FROM tx_ptextbase_tree_node WHERE lft >= " . $left . " AND rgt <= " . $right;
        #echo "DELTE query: " . $query;
        $extQuery = $this->createQuery();
        $extQuery->getQuerySettings()->setReturnRawQueryResult(true); // Extbase WTF
        $extQuery->statement($query)->execute();
	}
	
}
?>