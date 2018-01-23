<?php
namespace PunktDe\PtExtbase\Tests\ViewHelpers\Format;

/*
 * This file is part of the PunktDe\PtExtbase package.
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use PunktDe\PtExtbase\ViewHelpers\Format\FileSizeViewHelper;

class FileSizeViewHelperTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /**
     *
     * @returns array
     */
    public function fileSizeDataProvider()
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
     * @param string $input
     * @param bool $useIecLabel
     * @param string $formatedOutput
     *
     * @test
     * @dataProvider fileSizeDataProvider
     */
    public function render(string $input, bool $useIecLabel, string $formatedOutput)
    {
        $viewHelper =
            $this->getMockBuilder(FileSizeViewHelper::class)
                ->setMethods(['renderChildren'])
                ->getMock();

        $viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue($input));
        $actualResult = $viewHelper->render('', $useIecLabel);
        $this->assertEquals($formatedOutput, $actualResult);
    }
}
