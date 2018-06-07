<?php
namespace PunktDe\PtExtbase\ViewHelpers\Tree;
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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper renders the path of a category
 *
 * @author Daniel Lienert
 * @package pt_extbase
 * @subpackage ViewHelpers\Category
 */
class PathViewHelper extends AbstractViewHelper
{
    /**
     * @var \Tx_PtExtbase_Tree_NodePathBuilder
     */
    protected $nodePathBuilder;


    /**
     * @param \Tx_PtExtbase_Tree_NodePathBuilder $nodePathBuilder
     */
    public function injectNodePathBuilder(\Tx_PtExtbase_Tree_NodePathBuilder $nodePathBuilder)
    {
        $this->nodePathBuilder = $nodePathBuilder;
    }


    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('repository', 'string', 'Specifies the node repository', true);
        $this->registerArgument('namespace', 'string', 'Specifies the tree namespace', true);
        $this->registerArgument('node', 'integer', 'The node uid', true);
        $this->registerArgument('skipRoot', 'boolean', 'Skip the root node', false, false);
        $this->registerArgument('startIndex', 'integer', 'Start at Node', false, 0);
        $this->registerArgument('length', 'integer', 'Length of path', false, 1000);
    }


    /**
     * @return string The output
     */
    public function render()
    {
        $this->nodePathBuilder = \Tx_PtExtbase_Tree_NodePathBuilder::getInstanceByRepositoryAndNamespace(
            $this->arguments['repository'], $this->arguments['namespace']
        );

        $nodes = $this->getPathFromRootToNode();
        $firstNode = true;

        if (count($nodes) === 0) {
            $result = 'The node with the id ' . $this->arguments['node'] . ' could not be found in the given tree.';
        } else {
            $result = '';

            foreach ($nodes as $node) {
                $this->templateVariableContainer->add('node', $node);
                $this->templateVariableContainer->add('firstNode', $firstNode);

                $result .= $this->renderChildren();

                $this->templateVariableContainer->remove('node');
                $this->templateVariableContainer->remove('firstNode');

                $firstNode = false;
            }
        }

        return $result;
    }



    /**
     * @return array nodes in the path from root to node
     */
    protected function getPathFromRootToNode()
    {
        $node = (int)$this->arguments['node'];
        $length = (int)$this->arguments['length'];

        if ($this->arguments['skipRoot']) {
            $startIndex = 1;
        } else {
            $startIndex = (int)$this->arguments['startIndex'];
        }


        $nodes = $this->nodePathBuilder->getPathFromRootToNode($node, $startIndex, $length);

        return $nodes;
    }
}
