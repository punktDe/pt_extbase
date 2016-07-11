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

class Tx_PtExtbase_Tests_Unit_ViewHelpers_Format_FileSizeViewHelperTest extends \PunktDe\PtExtbase\Tests\Unit\AbstractBaseTestcase
{
    /**
     *
     * @returns array
     */
    public static function fileSizeDataProvider()
    {
        return array(
            'Bytes ' => array('145', '145 '),
            'KiloBytes ' => array('1450', '1.4 K'),
            'MegaBytes' => array('2540000', '2.4 M'),
            'Gigabytes' => array('1234567890', '1.1 G')
        );
    }


    /**
    * @test
    * @dataProvider fileSizeDataProvider
    */
    public function render($input, $formatedOutput)
    {
        $viewHelper = $this->getMock('Tx_PtExtbase_ViewHelpers_Format_FileSizeViewHelper', array('renderChildren'));
        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($input));
        $actualResult = $viewHelper->render();
        $this->assertEquals($formatedOutput, $actualResult);
    }
}
