<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

use TYPO3\CMS\Extbase\Persistence\Repository;

class NodeRepository extends Repository implements NodeRepositoryInterface
{
    /**
     * @var bool
     */
    protected $showDeleted = false;


    /**
     * @var TreeContext
     */
    protected $treeContext;


    /**
     * @param TreeContext $treeContext
     */
    public function injectTreeContext(TreeContext $treeContext)
    {
        $this->treeContext = $treeContext;
    }


    /**
     * Returns a set of nodes determined by the root of the given node.
     *
     * TODO rename: we do not find by nodeUid but by node object
     *
     * @param NodeInterface $node
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Node>
     */
    public function findByRootOfGivenNodeUid(NodeInterface $node)
    {
        $rootUid = $node->getRoot();
        return $this->findByRootUid($rootUid);
    }



    /**
     * Updates a given node if it has already been added to repository or adds it.
     *
     * @param NodeInterface $node
     */
    public function updateOrAdd(NodeInterface $node)
    {
        if ($node->getUid() === null || $node->getUid() < 0) {
            // UID of node < 0 means, node has not yet been persisted!
            $node->markAsNew();
            $this->add($node);
        } else {
            // UID of node > 0 means, node has been persisted before!
            $this->update($node);
        }
    }

    
    
    /**
     * Returns a set of nodes determined by uid of root node
     *
     * @param integer $rootUid
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Node>
     */
    public function findByRootUid($rootUid)
    {
        $query = $this->createQuery();
        $query->matching($query->equals('root', $rootUid))
            ->setOrderings(['lft' => \TYPO3\CMS\Extbase\Persistence\Generic\Query::ORDER_DESCENDING]);
        return $query->execute();
    }



    /**
     * Returns set of nodes for given namespace.
     * We return every node - if accessible or not, but mark an accessible on the node.
     * The flag is than later respected when the rendering is done.
     *
     * @param $namespace
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Node>
     */
    public function findByNamespace($namespace)
    {
        $nodes = $this->retrieveByNamespace($namespace, false);

        $accessibleNodes = $this->retrieveByNamespace($namespace, true);
        $this->markNodesAccessible($nodes, $accessibleNodes);

        return $nodes;
    }



    /**
     * @param $nodes
     * @param $accessibleNodes
     */
    protected function markNodesAccessible(&$nodes, $accessibleNodes)
    {
        $accessibleNodeUidArray = [];
        foreach ($accessibleNodes as $accessibleNode) {
            $accessibleNodeUidArray[$accessibleNode->getUid()] = $accessibleNode->getUid();
        }

        foreach ($nodes as $node) { /** @var $node Node */
            if (in_array($node->getUid(), $accessibleNodeUidArray)) {
                $node->setAccessible(true);
            } else {
                $node->setAccessible(false);
            }
        }
    }



    /**
     * @param $namespace
     * @param $respectEnableFields
     * @return array
     */
    protected function retrieveByNamespace($namespace, $respectEnableFields)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()
                  ->setRespectStoragePage(false)
                  ->setRespectSysLanguage(false)
                  ->setIgnoreEnableFields(!$respectEnableFields);

        // It is not possible to set IgnoreEnableFields to false and includeDeleted to true
        if($this->treeContext->isIncludeDeleted() && !$respectEnableFields) {
            $query->getQuerySettings()->setIncludeDeleted(true);
        }


        $nameSpaceConstraint = $query->equals('namespace', $namespace);

        /*
         * RespectEnableFields = FALSE means, that all records are selected INCLUDING the deleted records
         */
        if (!$respectEnableFields    && !$this->treeContext->isIncludeDeleted()) {
            $query->matching(
                $query->logicalAnd(
                    $nameSpaceConstraint,
                    $query->equals('deleted', '0')
                )
            );
        } else {
            $query->matching($nameSpaceConstraint);
        }

        $query->setOrderings(['lft' => \TYPO3\CMS\Extbase\Persistence\Generic\Query::ORDER_DESCENDING]);

        return $query->execute();
    }



    /**
     * Removes a node and its child nodes
     *
     * TODO as long as we only operate on trees, we don't need this. This is only required if we remove a single node out of tree-scope
     *
     * @param Node $node Node to be removed
     */
    public function remove($node)
    {

        /*
         * WARNING Whenever this goes productive, we have to make sure,
         * that the node table is write locked, while we
         * do the following three queries!
         */

        // We delete all database records that are no longer required
        $this->deleteNode($node);

        // We update object structure
        if (!$node->isRoot()) {
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
            $left = $node->getLft();
            $right = $node->getRgt();
            $difference = intval($right - $left + 1);

            // We update case 1. from above
            $query1 = "UPDATE node " .
                      "SET lft = lft - " . $difference . ", rgt = rgt - " . $difference . " " .
                      "WHERE namespace = \"" . $node->getNamespace() . "\" " .
                      "AND lft > " . $node->getLft();
            #echo "Update 1: " . $query1;
            $extQuery1 = $this->createQuery();
            $extQuery1->statement($query1)->execute(true);

            // We update case 2. from above
            $query2 = "UPDATE node " .
                      "SET rgt = rgt - " . $difference . " " .
                      "WHERE namespace = \"" . $node->getNamespace() . "\" " .
                      "AND lft < " . $node->getLft() . " " .
                      "AND rgt > " . $node->getRgt();
            #echo "Update 2: " . $query2;
            $extQuery2 = $this->createQuery();
            $extQuery2->statement($query2)->execute(true);
        }
    }
    
    
    
    /**
     * Hard-deletes a node and its child nodes from database.
     *
     * Warning: No deleted=1 is set in node record, nodes are really deleted!
     *
     * @param Node $node
     */
    protected function deleteNode(Node $node)
    {
        $left = $node->getLft();
        $right = $node->getRgt();
        
        $query = "DELETE FROM node WHERE lft >= " . $left . " AND rgt <= " . $right;
        $extQuery = $this->createQuery();
        $extQuery->statement($query)->execute(true);
    }
}
