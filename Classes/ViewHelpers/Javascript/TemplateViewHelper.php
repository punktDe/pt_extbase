<?php
namespace PunktDe\PtExtbase\ViewHelpers\Javascript;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Michael Knoll <mimi@kaktusteam.de>, MKLV GbR
 *
 *
 *  All rights reserved
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

use PunktDe\PtExtbase\Utility\HeaderInclusion;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class implements a viewhelper for inline javascript
 *
 * @author Daniel Lienert <daniel@lienert.cc>
 * @package ViewHelpers
 * @subpackage Javascript
 *
 * Available generic markers:
 *
 * extPath: relative path to the extension
 * extKey: Extension Key
 * pluginNamespace: Plugin Namespace for GET/POST parameters
 */
class TemplateViewHelper extends AbstractViewHelper
{

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Relative extpath to the extension (eg typo3conf/ext/pt_extbase/)
     *
     * @var string
     */
    protected $relExtPath;


    /**
     * @var string
     */
    protected $typo3Path;


    /**
     * Absolute ExtPath
     *
     * @var string
     */
    protected $extPath;


    /**
     * Extension key
     *
     * @var string
     */
    protected $extKey;


    /**
     * ViewHelper marker arguments
     * @var array
     */
    protected $markerArguments = [];


    /**
     * @var ExtensionService
     */
    protected $extensionService;

    /**
     * @param ExtensionService $extensionService
     */
    public function injectExtensionService(ExtensionService $extensionService): void
    {
        $this->extensionService = $extensionService;
    }


    /**
     * Initialize ViewHelper
     *
     * @return void
     */
    public function initialize()
    {
        if ($this->controllerContext instanceof \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext) {
            $this->extKey = $this->controllerContext->getRequest()->getControllerExtensionKey();
        } else {
            $this->extKey = 'pt_extbase';
        }

        $this->extPath = ExtensionManagementUtility::extPath($this->extKey);
        $this->relExtPath = '/' . PathUtility::stripPathSitePrefix(ExtensionManagementUtility::extPath($this->extKey));

        if (TYPO3_MODE === 'BE') {
            $this->initializeBackend();
        } else {
            $this->initializeFrontend();
        }
    }


    /**
     * Initialize Backend specific variables
     *
     * @return void
     */
    protected function initializeBackend()
    {
        $this->relExtPath = '../' . $this->relExtPath;
        $this->typo3Path = $GLOBALS['BACK_PATH'];
    }


    /**
     * Initialize Frontend specific variables
     *
     * @return void
     */
    protected function initializeFrontend()
    {
        $this->typo3Path = '/' . $GLOBALS['TSFE']->absRefPrefix . 'typo3/';
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('templatePath', 'string', 'The template path', true);
        $this->registerArgument('addToHead', 'boolean', 'Add to head section or return it a the place the viewhelper is', false, true);
        $this->registerArgument('compress', 'boolean', 'Whether to compress the JavaScript', false, true);
        $this->registerArgument('arguments', 'array|string', 'Arguments', false, '');
    }


    /**
     * View helper for showing debug information for a given object
     *
     * @throws \Exception
     * @return string
     */
    public function render()
    {
        if(is_array($this->arguments['arguments'])) {
            $this->markerArguments = array_merge($this->markerArguments, $this->arguments['arguments']);
        }
        elseif(is_string($this->arguments['arguments']) && $this->arguments['arguments'] !== '') {
            $this->markerArguments['renderDebugInfo'] = $this->arguments['arguments'];
        }

        $this->addGenericArguments();

        $absoluteFileName = GeneralUtility::getFileAbsFileName($this->arguments['templatePath']);
        if (!file_exists($absoluteFileName)) {
            throw new \Exception('No JSTemplate found with path ' . $absoluteFileName . '. 1296554335');
        }

        if ($this->arguments['addToHead']) {
            GeneralUtility::makeInstance(ObjectManager::class)
                ->get(HeaderInclusion::class)
                ->addJsInlineCode($this->arguments['templatePath'], $this->substituteMarkers($this->loadJsCodeFromFile($absoluteFileName), $this->markerArguments), $this->arguments['compress']);
        } else {
            $jsOutput = "<script type=\"text/javascript\">\n";
            $jsOutput .= $this->substituteMarkers($this->loadJsCodeFromFile($absoluteFileName), $this->markerArguments);
            $jsOutput .= "\n</script>\n";

            return $jsOutput;
        }
    }


    /**
     * Add some generic arguments that might be useful
     *
     * @return void
     */
    protected function addGenericArguments()
    {
        $this->markerArguments['veriCode'] = $this->generateVeriCode();
        $this->markerArguments['extPath'] = $this->relExtPath;
        $this->markerArguments['typo3Path'] = $this->typo3Path;
        $this->markerArguments['extKey'] = $this->extKey;

        if (is_object($this->controllerContext)) {
            $this->markerArguments['pluginNamespace'] = $this->extensionService->getPluginNamespace(
                $this->controllerContext->getRequest()->getControllerExtensionName(),
                $this->controllerContext->getRequest()->getPluginName()
            );
        }
    }


    /**
     * Generates a veri code for session (see t3lib_userauth)
     *
     * @return string
     */
    protected function generateVeriCode()
    {
        $sessionId = null;
        if (TYPO3_MODE === 'BE') {
            global $BE_USER;
            $sessionId = $BE_USER->id;
        } else {
            $sessionId = $GLOBALS['TSFE']->fe_user->id;
        }
        return substr(md5($sessionId . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']), 0, 10);
    }


    /**
     * Load JavaScript code from file
     *
     * @param string $absoluteFileName The absolute file name
     * @throws \Exception
     * @return string JsCodeTemplate
     */
    protected function loadJsCodeFromFile($absoluteFileName)
    {
        $data = file_get_contents($absoluteFileName);

        if ($data === false) {
            throw new \Exception('Could not read the file content of file ' . $absoluteFileName . '! 1300865874');
        }

        return $data;
    }


    /**
     * Substitute Markers in Code
     *
     * @param string $jsCode JavaScript code
     * @param array $arguments ViewHelper arguments
     * @return string
     */
    protected function substituteMarkers($jsCode, $arguments)
    {
        $markers = $this->prepareMarkers($arguments);
        $this->addTranslationMarkers($jsCode, $markers);
        return str_replace(array_keys($markers), array_values($markers), $jsCode);
    }


    /**
     * Find LLL markers in the jsCode and arguments for them
     *
     * @param string $jsCode JavaScript code
     * @param array markers Markers
     */
    protected function addTranslationMarkers($jsCode, &$markers)
    {
        $matches = [];
        $pattern = '/\#\#\#LLL:.*\#\#\#/';
        preg_match_all($pattern, $jsCode, $matches);
        foreach ($matches[0] as $match) {
            $translateKey = substr($match, 7, -3);
            $markers[$match] = $this->translate($translateKey);
        }
    }


    /**
     * @param $translateKey
     * @return string The value from LOCAL_LANG or NULL if no translation was found.
     */
    protected function translate($translateKey)
    {
        return LocalizationUtility::translate($translateKey, $this->extKey);
    }


    /**
     * Prepare the markers
     *
     * @param array $arguments
     * @return array
     */
    protected function prepareMarkers($arguments)
    {
        $markers = [];
        foreach ($arguments as $key => $value) {
            $markers['###' . $key . '###'] = $value;
        }
        return $markers;
    }
}
