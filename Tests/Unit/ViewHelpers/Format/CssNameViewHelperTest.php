<?php
/***************************************************************
* Copyright notice
*
*   2011 Daniel Lienert <daniel@lienert.cc>, Michael Knoll <mimi@kaktusteam.de>
* All rights reserved
*
*
* This script is part of the TYPO3 project. The TYPO3 project is
* free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


/**
*
* @package Tests
* @subpackage ViewHelpers/Format
* @author Daniel Lienert
*/

class Tx_PtExtbase_Tests_Unit_ViewHelpers_Format_CssNameViewHelperTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /**
     *
     * @returns array
     */
    public static function nameDataProvider()
    {
        return array(
            'camelCase ' => array('DasIstEinTest', 'das-ist-ein-test'),
            'Spaces ' => array('Das ist ein Test', 'das-ist-ein-test'),
            'Spaces before and after ' => array(' Das ist ein Test ', 'das-ist-ein-test'),
        );
    }


    /**
    * @test
    * @dataProvider nameDataProvider
    */
    public function render($input, $formatedOutput)
    {
        $viewHelper = $this->getMock('Tx_PtExtbase_ViewHelpers_Format_CssNameViewHelper', array('renderChildren'));
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($input));
        
        $actualResult = $viewHelper->render();
        $this->assertEquals($formatedOutput, $actualResult);
    }
}
