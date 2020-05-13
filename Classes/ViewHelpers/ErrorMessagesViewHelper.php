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

use PunktDe\PtDpppBase\Domain\Model\Account;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

class ErrorMessagesViewHelper extends AbstractTagBasedViewHelper
{

    /**
     * @var LocalizationUtility
     */
    protected $localization;

    /**
     * @param LocalizationUtility $localization
     */
    public function injectLocalization(LocalizationUtility $localization): void
    {
        $this->localization = $localization;
    }

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('extension', 'string', 'The extension');
        $this->registerArgument('file', 'string', 'The file');
    }

    /**
     * @param string $extension
     * @param string $file
     *
     * @return mixed
     */
    public function render()
    {
        $extension = $this->arguments['extension'];
        $file = $this->arguments['file'] ?? 'errors.xlf';

        $validationResults = $this->renderingContext->getControllerContext()->getRequest()->getOriginalRequestMappingResults()->getFlattenedErrors();

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
