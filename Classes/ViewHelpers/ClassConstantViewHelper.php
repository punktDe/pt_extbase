<?php
namespace PunktDe\PtExtbase\ViewHelpers;

class ClassConstantViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @param string $className
     * @param string $constantName
     * @return mixed
     */
    public function render($className, $constantName)
    {
        return constant(sprintf('%s::%s', $className, $constantName));
    }
}
