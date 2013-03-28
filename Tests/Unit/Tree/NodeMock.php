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
 * Node mock. Helps us setting an arbitrary UID which is not easy to do since
 * the UID is stuff is final in domain objects.
 *
 * @package Tests
 * @subpackage Tree
 * @author Michael Knoll <mimi@kaktusteam.de>
 */
class Tx_PtExtbase_Tests_Unit_Tree_NodeMock extends Tx_PtExtbase_Tree_Node {

	/**
	 * Helper method to create a node mock object
	 *
	 * @param $uid
	 * @param int $lft
	 * @param int $rgt
	 * @param int $root
	 * @param string $label
	 * @param string $namespace
	 * @param bool $accessible
	 * @return Tx_PtExtbase_Tree_Node
	 */
    public static function createNode($uid, $lft, $rgt, $root, $label = '', $namespace = '', $accessible = TRUE) {
        $node = new Tx_PtExtbase_Tests_Unit_Tree_NodeMock($uid, $label, $namespace);
        $node->setLft($lft);
        $node->setRgt($rgt);
        $node->setRoot($root);
	    $node->setAccessible($accessible);
        return $node;
    }



	public function __construct($uid = null, $label = null, $namespace = null) {
		parent::__construct($label);
		$this->uid = $uid;
        $this->setNamespace($namespace);
	}



	public function setUid($uid) {
		$this->uid = $uid;
	}

}
?>