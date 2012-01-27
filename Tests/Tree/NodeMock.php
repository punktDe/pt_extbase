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
 * @subpackage Category
 * @author Michael Knoll <mimi@kaktusteam.de>
 */
class Tx_PtExtbase_Tests_Tree_NodeMock extends Tx_PtExtbase_Tree_Node {

    /**
     * Helper method to create a category object
     *
     * @param int $lft
     * @param int $rgt
     * @param int $root
     * @param string $name
     * @param string $description
     * @return Tx_PtExtbase_Tree_Node
     */
    public static function createCategory($uid, $lft, $rgt, $root, $name = '', $description = '') {
        $category = new Tx_PtExtbase_Tests_Tree_NodeMock($uid, $name, $description);
        $category->setLft($lft);
        $category->setRgt($rgt);
        $category->setRoot($root);
        return $category;
    }



	public function __construct($uid = null, $name = null, $description = null) {
		$this->uid = $uid;
		parent::__construct($name, $description);
	}



	public function setUid($uid) {
		$this->uid = $uid;
	}

}
?>