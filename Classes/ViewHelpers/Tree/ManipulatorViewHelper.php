<?php
namespace PunktDe\PtExtbase\ViewHelpers\Tree;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert
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

use PunktDe\PtDpppBase\Utility\SessionAdapter;
use PunktDe\PtExtbase\ViewHelpers\Javascript\TemplateViewHelper;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Form\TextfieldViewHelper;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class implements a widget viewhelper for rendering trees that can be manipulated using ajax requests
 *
 * @author Daniel Lienert
 */
class ManipulatorViewHelper extends TextfieldViewHelper
{
    /**
     * Initialize arguments.
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('repository', 'string', 'Specifies the tree repository', false);
        $this->registerArgument('namespace', 'string', 'Specifies the tree namespace', false);
        $this->registerArgument('respectEnableFields', 'boolean', 'Should the tree respect enable fields', false);
        $this->registerArgument('moduleName', 'string', 'Specify the module name', false);
    }


    /**
     * @param bool $required If the field is required or not
     * @param string $type
     * @return string
     * @throws \Exception
     */
    public function render()
    {
        $treeDiv = $this->getTreeDiv();
        $treeJS = $this->getTreeJS();

        return $treeDiv . $treeJS;
    }


    /**
     * @return string
     * @throws \Exception
     */
    protected function getTreeJS()
    {

        /** @var TemplateViewHelper $treeViewHelper  */
        $treeViewHelper = GeneralUtility::makeInstance(ObjectManager::class)->get(TemplateViewHelper::class);

        $moduleUrl = '';
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        if (isset($this->arguments['moduleName'])) {
            $moduleUrl = $uriBuilder->buildUriFromRoute($this->arguments['moduleName']);
        }
        $editRecordUrlUrl = $uriBuilder->buildUriFromRoute('record_edit');

        $arguments = [
            'templatePath' => 'EXT:pt_extbase/Resources/Private/JSTemplates/Tree/ManipulationTree.js',
            'arguments' => [
                'baseUrl' => $this->getBaseURL(),
                'dbNodeTable' => 'tx_ptcertification_domain_model_category',
                'moduleUrl' => $moduleUrl,
                'editRecord' => $editRecordUrlUrl
            ],

            'addToHead' => false,
            'compress' => false
        ];
        $treeViewHelper->setArguments($arguments);
        return $treeViewHelper->render();
    }



    /**
     * @return string
     */
    protected function getTreeDiv()
    {
        return '<div id="ptExtbaseTreeDiv"></div>';
    }


    /**
     * Save settings to user session
     * @throws \Exception
     */
    protected function saveTreeSettingsToSession()
    {
        $treeSettings = [
            'repository' => $this->arguments['repository'],
            'namespace' => $this->arguments['namespace'],
            'respectEnableFields' => $this->arguments['respectEnableFields'],
        ];

        GeneralUtility::makeInstance(SessionAdapter::class)->store('Tx_PtExtbase_Tree_Configuration', $treeSettings);
    }



    /**
     * Determine the baseURl by context
     * @return string
     */
    protected function getBaseURL()
    {
        if (TYPO3_MODE === 'BE') {
            $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
            $baseUrl = $uriBuilder->buildUriFromRoute('ptxTree');
        } elseif (TYPO3_MODE === 'FE') {
            // TODO: define frontend correctly
            $baseUrl = 'index.php?eID=ptxAjax';
        }

        return $baseUrl;
    }
}
