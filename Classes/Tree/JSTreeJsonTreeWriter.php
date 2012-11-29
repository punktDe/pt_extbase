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
 * Writer outputs JSON notation of a tree
 *
 * @package Tree
 * @author Sebastian Helzle <helzle@punkt.de>
 */
class Tx_PtExtbase_Tree_JSTreeJsonTreeWriter extends Tx_PtExtbase_Tree_JsonTreeWriter {

    /**
     * Creates a new instance of json writer
     *
     * @param array $visitors
     * @return Tx_PtExtbase_Tree_JsonTreeWriter
     */
    public static function getInstance(array $visitors = array()) {
        $arrayWriterVisitor = new Tx_PtExtbase_Tree_JSTreeJsonWriterVisitor();
        $visitors[] = $arrayWriterVisitor;
        $jsonTreeWriter = new Tx_PtExtbase_Tree_JsonTreeWriter($visitors, $arrayWriterVisitor);
        return $jsonTreeWriter;
    }
}
?>