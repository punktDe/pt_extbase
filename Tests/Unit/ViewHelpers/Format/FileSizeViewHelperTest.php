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
class Tx_PtExtbase_Tests_Unit_ViewHelpers_Format_FileSizeViewHelperTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /**
     *
     * @returns array
     */
    public static function fileSizeDataProvider()
    {
        return [
            'Bytes' => ['145', false, '145 '],
            'KiloBytes' => ['1450', false, '1.42 K'],
            'MegaBytes' => ['2540000', false, '2.42 M'],
            'Gigabytes' => ['1234567890', false, '1.15 G'],
            'BytesUseIecLabel' => ['145', true, '145 '],
            'KiloBytesUseIecLabel' => ['1450', true, '1.42 Ki'],
            'MegaBytesUseIecLabel' => ['2540000', true, '2.42 Mi'],
            'GigabytesUseIecLabel' => ['1234567890', true, '1.15 Gi']
        ];
    }


    /**
     * @test
     * @dataProvider fileSizeDataProvider
     */
    public function render($input, $useIecLabel, $formatedOutput)
    {
        $viewHelper =
            $this->getMockBuilder(Tx_PtExtbase_ViewHelpers_Format_FileSizeViewHelper::class)
                ->setMethods(['renderChildren'])
                ->getMock();

        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($input));
        $actualResult = $viewHelper->render('', $useIecLabel);
        $this->assertEquals($formatedOutput, $actualResult);
    }
}
