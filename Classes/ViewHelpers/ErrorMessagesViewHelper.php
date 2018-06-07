<?php
namespace PunktDe\PtExtbase\ViewHelpers;
/*
 * Copyright notice
 *
 * (c) 2012/2013 Christian Herberger <webmaster@kabarakh.de>
 *
 * All rights reserved
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

class ErrorMessagesViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var \TYPO3\CMS\Extbase\Utility\LocalizationUtility
     * @inject
     */
    protected $localization;

    /**
     * @param string $extension
     * @param string $file
     *
     * @return mixed
     */
    public function render($extension, $file = 'errors.xlf')
    {
        $validationResults = $this->controllerContext->getRequest()->getOriginalRequestMappingResults()->getFlattenedErrors();

        $output = '';

        foreach ($validationResults as $propertyError) {
            foreach ($propertyError as $error) { /** @var \TYPO3\CMS\Extbase\Validation\Error $error */
                $translatedMessage = $this->localization->translate('LLL:EXT:' . $extension . '/Resources/Private/Language/' . $file . ':' . $error->getMessage(), $extension);

                if (empty($translatedMessage)) {
                    $translatedMessage = $error->getMessage();
                }

                $this->templateVariableContainer->add('errorMessage', $translatedMessage);
                $output .= $this->renderChildren();
                $this->templateVariableContainer->remove('errorMessage');
            }
        }

        return $output;
    }
}
