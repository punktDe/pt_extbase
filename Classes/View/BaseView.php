<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll
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

/**
 * Class implements base view with some enhanced features for extension development with Extbase 
 * 
 * @author Michael Knoll 
 * @author Daniel Lienert 
 * @package View
 */
class Tx_PtExtbase_View_BaseView extends Tx_Fluid_View_TemplateView {

	/**
	 * Directory pattern for global partials. Not part of the public API, should not be changed for now.
	 * @var string
	 */
	private $partialPathAndFilenamePattern = '@partialRoot/@partial.@format';
	
	
	
	/**
	 * Pattern to be resolved for @templateRoot in the other patterns.
	 * @var string
	 */
	protected $templateRootPathPattern = '@packageResourcesPath/Private/Templates';
	
	
	
	/**
	 * (non-PHPdoc)
	 * @see Classes/View/Tx_Fluid_View_TemplateView::initializeView()
	 */
	public function initializeView() {
	}



	/**
	 * (non-PHPdoc)
	 * @see Classes/View/Tx_Fluid_View_TemplateView::getPartialSource()
	 *
	 * @param string $partialName The name of the partial
	 * @return string the full path which should be used. The path definitely exists.
	 * @throws Tx_Fluid_View_Exception_InvalidTemplateResourceException
	 */
	protected function getPartialSource($partialName) {

		/**
		 * As in 1.3.0 resolving was part of this method we had to overwrite the complete method
		 * This is not longer necessary in Version 1.4.0
		 */
		if(substr(t3lib_extMgm::getExtensionVersion('fluid'),0,3) == '1.4') return parent::getPartialSource($partialName);

		$paths = $this->expandGenericPathPattern($this->partialPathAndFilenamePattern, TRUE, TRUE);

		$paths[] = $this->getPartialPathAndFilename($partialName);

		$found = FALSE;
		foreach ($paths as &$partialPathAndFilename) {
			$partialPathAndFilename = str_replace('@partial', $partialName, $partialPathAndFilename);
			if (file_exists($partialPathAndFilename)) {
				$found = TRUE;
				break;
			}
		}

		if (!$found) {
			throw new Tx_Fluid_View_Exception_InvalidTemplateResourceException('The template files "' . implode('", "', $paths) . '" could not be loaded.', 1225709595);
		}

		$partialSource = file_get_contents($partialPathAndFilename);
		if ($partialSource === FALSE) {
			throw new Tx_Fluid_View_Exception_InvalidTemplateResourceException('"' . $partialPathAndFilename . '" is not a valid template resource URI.', 1257246929);
		}
		return $partialSource;
	}



	/**
	 * Resolve the template path and filename for the given action. If $actionName
	 * is NULL, looks into the current request.
	 *
	 * @param string $actionName Name of the action. If NULL, will be taken from request.
	 * @return string Full path to template
	 * @throws Tx_Fluid_View_Exception_InvalidTemplateResourceException
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	protected function getTemplateSource($actionName = NULL) {

		/**
		 * As in 1.3.0 resolving was part of this method we had to overwrite the complete method
		 * This is not longer necessary in Version 1.4.0
		 */
		if(substr(t3lib_extMgm::getExtensionVersion('fluid'),0,3) == '1.4') return parent::getTemplateSource($actionName);
		
		if ($this->templatePathAndFilename !== NULL) {
			$templatePathAndFilename = $this->templatePathAndFilename;
		} else {
			$actionName = ($actionName !== NULL ? $actionName : $this->controllerContext->getRequest()->getControllerActionName());
			$actionName = ucfirst($actionName);

			$paths = $this->expandGenericPathPattern($this->templatePathAndFilenamePattern, FALSE, FALSE);
			$paths[] = $this->getTemplatePathAndFilename($actionName);

			$found = FALSE;
			foreach ($paths as &$templatePathAndFilename) {
				// These tokens are replaced by the Backporter for the graceful fallback in version 4.
				$fallbackPath = str_replace('@action', strtolower($actionName), $templatePathAndFilename);
				$templatePathAndFilename = str_replace('@action', $actionName, $templatePathAndFilename);
				if (file_exists($templatePathAndFilename)) {
					$found = TRUE;
					break;
				}  elseif (file_exists($fallbackPath)) {
					$found = TRUE;
					$templatePathAndFilename = $fallbackPath;
					t3lib_div::deprecationLog('the template filename "' . $fallbackPath . '" is lowercase. This is deprecated since TYPO3 4.4. Please rename the template to "' . basename($templatePathAndFilename) . '"');
					break;
				}
			}
			if (!$found) {
				throw new Tx_Fluid_View_Exception_InvalidTemplateResourceException('Template could not be loaded. I tried "' . implode('", "', $paths) . '"', 1225709595);
			}
		}

		$templateSource = file_get_contents($templatePathAndFilename);
		if ($templateSource === FALSE) {
			throw new Tx_Fluid_View_Exception_InvalidTemplateResourceException('"' . $templatePathAndFilename . '" is not a valid template resource URI.', 1257246929);
		}
		return $templateSource;
	}



	/**	  
	 * 
     * Figures out which partial to use.
     * 
     * We overwrite this method to make sure that we can use something like this in our partial:
     * 
     * partialPath = EXT:pt_extbase/Resources/Private/Partials/Filter/StringFilter.html
     *
     * @param string $partialName The name of the partial
     * @return string the full path which should be used. The path definitely exists.
     * @throws Tx_Fluid_View_Exception_InvalidTemplateResourceException
     */
	protected function getPartialPathAndFilename($partialName) {
		if (file_exists($partialName)) { // partial is given as absolute path (rather unusual :-) )
			return $partialName;
		} elseif (file_exists(t3lib_div::getFileAbsFileName($partialName))) { // partial is given as EXT:pt_extbase/Resources/Private/Partials/Filter/StringFilter.html
			return t3lib_div::getFileAbsFileName($partialName);
		} else {
			if(method_exists('Tx_Fluid_View_TemplateView','getPartialPathAndFilename')) {
				return parent::getPartialPathAndFilename($partialName); // this method only exists in 1.4.0
			} else {
				return $partialName;
			}
		}
	}
	
	
	/**
     * Resolve the template path and filename for the given action. If $actionName
     * is NULL, looks into the current request.
     * 
     * Tries to read template path and filename from current settings.
     * Path can be set there by $controller->setTemplatePathAndFilename(Path to template)
     *
     * @param string $actionName Name of the action. If NULL, will be taken from request.
     * @return string Full path to template
     * @throws Tx_Fluid_View_Exception_InvalidTemplateResourceException
     */
	protected function getTemplatePathAndFilename($actionName = NULL) {

		if ($this->templatePathAndFilename != '') {

			if (file_exists($this->templatePathAndFilename)) {
			    return $this->templatePathAndFilename;
			}

			if (file_exists(t3lib_div::getFileAbsFileName($this->templatePathAndFilename))) {
				return t3lib_div::getFileAbsFileName($this->templatePathAndFilename);
			}

		} else {
			if(method_exists('Tx_Fluid_View_TemplateView', 'getTemplatePathAndFilename')) {
				return parent::getTemplatePathAndFilename($actionName); // this method only exists in 1.4.0
			} else {
				return $actionName;
			}
		}
	}
	
}