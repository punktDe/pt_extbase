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
 * Testcase for json tree writer
 *
 * @package Tests
 * @subpackage Functional\Tree
 * @author Michael Knoll <knoll@punkt.de>
 */
class Tx_PtExtbase_Tests_Functional_Tree_JsonTreeWalkerTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {

    /** @test */
    public function jsonTreeWriterWritesExpectedJsonForGivenTree() {
        $jsonTreeWriter = Tx_PtExtbase_Tree_JsonTreeWriter::getInstance();

        $tree = Tx_PtExtbase_Tree_Tree::getEmptyTree('root');
        $rootNode = $tree->getRoot();
        $firstNode = new Tx_PtExtbase_Tree_Node('firstNode');
        $secondNode = new Tx_PtExtbase_Tree_Node('secondNode');
        $firstChildOfSecondNode = new Tx_PtExtbase_Tree_Node('firstChildOfSecondNode');

        $secondNode->addChild($firstChildOfSecondNode);
        $rootNode->addChild($firstNode);
        $rootNode->addChild($secondNode);

        $jsonString = $jsonTreeWriter->writeTree($tree);
        $this->assertEquals('[{"uid":' . $rootNode->getUid() . ',"label":"root","children":[{"uid":' . $firstNode->getUid() . ',"label":"firstNode","children":[]},{"uid":' . $secondNode->getUid() . ',"label":"secondNode","children":[{"uid":'.$firstChildOfSecondNode->getUid().',"label":"firstChildOfSecondNode","children":[]}]}]}]', $jsonString);
    }



    /** @test */
    public function jsonTreeWriterRespectsRestrictedLevelsInWrittenTrees() {
        $tree = Tx_PtExtbase_Tree_Tree::getEmptyTree('root');

        $rootNode = $tree->getRoot();
        $firstNode = new Tx_PtExtbase_Tree_Node('firstNode');
        $secondNode = new Tx_PtExtbase_Tree_Node('secondNode');
        $firstChildOfSecondNode = new Tx_PtExtbase_Tree_Node('firstChildOfSecondNode');

        $secondNode->addChild($firstChildOfSecondNode);
        $rootNode->addChild($firstNode);
        $rootNode->addChild($secondNode);

        $tree->setRespectRestrictedDepth(TRUE);

        $jsonTreeWriter = Tx_PtExtbase_Tree_JsonTreeWriter::getInstance();

        // Restricting level to 2 (two children should be rendered)
        $tree->setRestrictedDepth(2);
        $jsonStringForDepth2 = $jsonTreeWriter->writeTree($tree);
        $this->assertEquals('[{"uid":' . $rootNode->getUid() . ',"label":"root","children":[{"uid":' . $firstNode->getUid() . ',"label":"firstNode","children":[]},{"uid":' . $secondNode->getUid() . ',"label":"secondNode","children":[]}]}]', $jsonStringForDepth2);

        // Restricting level to 1 (only root should be rendered)
        $tree->setRestrictedDepth(1);
        $jsonStringForDepth1 = $jsonTreeWriter->writeTree($tree);
        $this->assertEquals('[{"uid":' . $rootNode->getUid() . ',"label":"root","children":[]}]',$jsonStringForDepth1);
    }



    /**
     * @return Tx_PtExtbase_Tree_Tree
     */
    protected function createTestTree() {
        $tree = Tx_PtExtbase_Tree_Tree::getEmptyTree('root');
        $rootNode = $tree->getRoot();
        $firstNode = new Tx_PtExtbase_Tree_Node('firstNode');
        $secondNode = new Tx_PtExtbase_Tree_Node('secondNode');
        $firstChildOfSecondNode = new Tx_PtExtbase_Tree_Node('firstChildOfSecondNode');

        $secondNode->addChild($firstChildOfSecondNode);
        $rootNode->addChild($firstNode);
        $rootNode->addChild($secondNode);

        return $tree;
    }
	
}
?>