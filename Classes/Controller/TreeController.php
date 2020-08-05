<?php
namespace PunktDe\PtExtbase\Controller;

use PunktDe\PtExtbase\State\Session\Storage\SessionAdapter;
use PunktDe\PtExtbase\Tree\ArrayTreeWriter;
use PunktDe\PtExtbase\Tree\JSArrayTreeWriter;
use PunktDe\PtExtbase\Tree\JSTreeJsonTreeWriter;
use PunktDe\PtExtbase\Tree\NodeRepository;
use PunktDe\PtExtbase\Tree\TreeContext;
use PunktDe\PtExtbase\Tree\TreeRepository;
use PunktDe\PtExtbase\Tree\TreeRepositoryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Annotation as Extbase;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2012 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Sebastian Helzle
 *  All rights reserved
 *
 *  For further information: http://extlist.punkt.de <extlist@punkt.de>
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
 * Class implements actions for tree manipulation with ajax calls
 *
 * @author Daniel Lienert
 * @author Michael Knoll
 * @author Sebastian Helzle
 */
class TreeController extends  \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;



    /**
     * @var NodeRepository
     */
    protected $nodeRepository;


    /**
     * @var string tree namespace
     */
    protected $treeNameSpace = 'default';


    /**
     * @var string
     */
    protected $nodeRepositoryClassName = NodeRepository::class;


    /**
     * @var TreeRepository
     */
    protected $treeRepository;



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
     * @param PersistenceManager $persistenceManager
     */
    public function injectPersistenceManager(PersistenceManager $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }


    /**
     * Initializes the current action
     *
     * @return void
     */
    protected function initializeAction()
    {
        if (TYPO3_MODE !== "BE") {
            die();
        }

        $this->restoreTreeSettingsFromSession();

        $treeRepositoryBuilder = TreeRepositoryBuilder::getInstance();
        $treeRepositoryBuilder->setNodeRepositoryClassName($this->nodeRepositoryClassName);
        $this->treeRepository = $treeRepositoryBuilder->buildTreeRepository();

        $this->nodeRepository = $this->objectManager->get($this->nodeRepositoryClassName);
    }



    /**
     * @todo If several tree widgets are needed on the same page, provide an identifier and select the right repo from session
     *
     * Restore the repository settings from namespace
     */
    public function restoreTreeSettingsFromSession()
    {
        $settings = GeneralUtility::makeInstance(SessionAdapter::class)->read('Tx_PtExtbase_Tree_Configuration');
        if (!is_array($settings)) {
            $settings = [
                'repository' => 'Tx_PtCertification_Domain_Repository_CategoryRepository',
                'namespace' => 'tx_ptcertification_domain_model_category',
                'respectEnableFields' => $this->treeContext->respectEnableFields(),
            ];
        }

        if (array_key_exists('repository', $settings)) {
            $nodeRepositoryClassName = $settings['repository'];
            if ($nodeRepositoryClassName && class_exists($nodeRepositoryClassName)) {
                $this->nodeRepositoryClassName = $nodeRepositoryClassName;
            }
        }

        if (array_key_exists('namespace', $settings)) {
            $this->treeNameSpace = $settings['namespace'];
        }
    }


    /**
     * Initializes settings for this controller
     */
    public function initializeSettings()
    {
        /**
         * @todo The tree namespace should be set by the viewHelper
         */
        $this->treeNameSpace = 'tx_ptextbase_tests_testNamespace';
    }



    /**
     * Get tree or subtree when node is given
     *
     * @Extbase\IgnoreValidation("node")
     * @Extbase\IgnoreValidation("restrictedDepth")
     * @param integer $node
     * @param integer $restrictedDepth
     * @return array
     */
    public function getTreeAction ($node=null, $restrictedDepth=0)
    {
        $this->initializeAction();
        if ($node) {
            $tree = $this->treeRepository->getEmptyTree($this->treeNameSpace);
        } else {
            $tree = $this->treeRepository->loadTreeByNamespace($this->treeNameSpace);
            if ($restrictedDepth > 0) {
                $tree->setRestrictedDepth($restrictedDepth);
                $tree->setRespectRestrictedDepth(true);
            }
        }

        return [JSArrayTreeWriter::getInstance()->writeTree($tree)];
    }



    /**
     * Adds new node into given parent node with given label
     *
     * @param integer $parent
     * @param string $label
     *
     * @return array
     */
    public function addNodeAction($parent, $label)
    {
        $this->initializeAction();

        $newNode = new \PunktDe\PtCertification\Domain\Model\Category($label);
        $tree = $this->treeRepository->loadTreeByNamespace($this->treeNameSpace);

        $parent = $tree->getNodeByUid($parent);

        $tree->insertNode($newNode, $parent);

        $this->treeRepository->update($tree);

        $this->persistenceManager->persistAll();

        $newNodeUid = $newNode->getUid() > 0 ? $newNode->getUid() : 0;

        return [
            "status" => ($newNodeUid > 0 ? 'true' : 'false'),
            "id" =>  $newNodeUid
            ];
    }



    /**
     * Removes given node from tree
     *
     * @param integer $node
     */
    public function removeNodeAction($node)
    {
        $this->initializeAction();
        $tree = $this->treeRepository->loadTreeByNamespace($this->treeNameSpace);

        $node = $tree->getNodeByUid($node);

        $tree->deleteNode($node);
        $this->treeRepository->update($tree);

        $this->persistenceManager->persistAll();

        return [
            "status" => 'true',
        ];
    }



    /**
     * Moves node into node
     *
     * This action can be used for a drag'n'drop of a node "onto" another node.
     *
     * @param integer $node Node to be moved
     * @param integer $targetNode Node where moved node should be put into
     */
    public function moveNodeIntoAction($node, $targetNode)
    {
        $this->initializeAction();
        $tree = $this->treeRepository->loadTreeByNamespace($this->treeNameSpace);

        $node = $tree->getNodeByUid($node);
        $targetNode = $tree->getNodeByUid($targetNode);

        $tree->moveNode($node, $targetNode);
        $this->treeRepository->update($tree);

        $this->persistenceManager->persistAll();

        return [
            "status" => 'true',
        ];
    }



    /**
     * Moves node given by ID after node given by ID as child of the very same node
     *
     * @param integer $node Node that has to be moved
     * @param integer $targetNode Node where moved node should be put before
     */
    public function moveNodeAfterAction($node, $targetNode)
    {
        $this->initializeAction();
        $tree = $this->treeRepository->loadTreeByNamespace($this->treeNameSpace);

        $node = $tree->getNodeByUid($node);
        $targetNode = $tree->getNodeByUid($targetNode);

        $tree->moveNodeAfterNode($node, $targetNode);
        $this->treeRepository->update($tree);

        $this->persistenceManager->persistAll();

        return [
            "status" => 'true',
        ];
    }



    /**
     * Moves node before targetNode as child of the very same node.
     *
     * @param integer $node ID of node that was moved
     * @param integer $targetNode ID of node where moved node should be put after
     */
    public function moveNodeBeforeAction($node, $targetNode)
    {
        $this->initializeAction();
        $tree = $this->treeRepository->loadTreeByNamespace($this->treeNameSpace);

        $node = $tree->getNodeByUid($node);
        $targetNode = $tree->getNodeByUid($targetNode);

        $tree->moveNodeBeforeNode($node, $targetNode);
        $this->treeRepository->update($tree);
        $this->persistenceManager->persistAll();

        return [
            "status" => 'true',
        ];
    }



    /**
     * Saves given node object with given label
     *
     * TODO Warning: As we do not check any properties set on the node here, user could manipulate lft and rgt values and hence crash the tree!
     *
     * @param integer $node
     * @param string $label
     */
    public function saveNodeAction($node, $label = '')
    {
        $this->initializeAction();
        $tree = $this->treeRepository->loadTreeByNamespace($this->treeNameSpace);

        $node = $tree->getNodeByUid($node);

        $node->setLabel($label);
        $this->nodeRepository->update($node);

        $this->persistenceManager->persistAll();

        return [
            "status" => 'true',
        ];
    }
}
