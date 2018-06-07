<?php
namespace PunktDe\PtExtbase\ViewHelpers;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class StringComparisonViewHelper extends AbstractViewHelper
{
    /**
     * @param string $input
     * @param string $expected
     * @return integer
     */
    public function render($input, $expected)
    {
        if ($input == $expected) {
            return 1;
        } else {
            return 0;
        }
    }
}
