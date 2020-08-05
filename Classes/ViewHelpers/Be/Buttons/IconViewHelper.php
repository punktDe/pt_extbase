<?php
namespace PunktDe\PtExtbase\ViewHelpers\Be\Buttons;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll
 *  All rights reserved
 *
 *  For further information: http://extlist.punkt.de <extlist@punkt.de>
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
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Type\Icon\IconState;
use TYPO3\CMS\Core\Utility\GeneralUtility;
/*
 * = Examples =
 *
 * <code title="Default">
 * <f:be.buttons.icon uri="{f:uri.action()}" />
 * </code>
 * <output>
 * An icon button as known from the TYPO3 backend, skinned and linked with the default action of the current controller.
 * Note: By default the "close" icon is used as image
 * </output>
 *
 * <code title="Default">
 * <f:be.buttons.icon uri="{f:uri.action(action:'new')}" icon="actions-document-new" title="Create new Foo" />
 * </code>
 * <output>
 * This time the "new_el" icon is returned, the button has the title attribute set and links to the "new" action of the current controller.
 * </output>
 *
 * @author Steffen Kamper <info@sk-typo3.de>
 * @author Bastian Waidelich <bastian@typo3.org>
 * @author Daniel Lienert <daniel@lienert.cc>
 *
 * @license http://www.gnu.org/copyleft/gpl.html
 */
class IconViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper
{
    protected $escapeOutput = false;

    /**
     * Register arguments.
     */
    public function initializeArguments()
    {
        $this->registerArgument('onclick', 'string', 'The onclick action', false);
		$this->registerArgument('additionalAttributes', 'array', 'Additional tag attributes. They will be added directly to the resulting HTML tag.', FALSE);
        $this->registerArgument('class', 'string', 'The class attribute', false);
        $this->registerArgument('uri', 'string', 'Uri', true);
        $this->registerArgument('icon', 'string', 'Icon', false, 'actions-document-close');
        $this->registerArgument('title', 'string', 'Title no used ', false, '');
    }


    /**
     * Renders an icon link as known from the TYPO3 backend
     *
     * @return string the rendered icon link
     */
    public function render()
    {
        if ($this->arguments['onclick']) {
            $onclick = ' onclick="'.$this->arguments['onclick'].'" ';
        }

        if ($this->arguments['class']) {
            $onclick = ' class="'.$this->arguments['class'].'" ';
        }
        /** @var IconFactory $iconFactory */
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);

        $ret = '<a href="' . $this->arguments['uri'] . '"' . $onclick . '>' .  $iconFactory->getIcon($this->arguments['icon'], Icon::SIZE_SMALL)->render() . '</a>';
        return $ret;
    }
}
