<?php
namespace PunktDe\PtExtbase\State\GpVars;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll, Christoph Ehscheidt
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
use PunktDe\PtExtbase\Utility\NamespaceUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * Class implements adapter for GET and POST vars to be used by
 * objects implementing the GpVarsInjectable interface
 */
class GpVarsAdapter
{
    /**
     * Holds array with post vars from current HTTP request
     *
     * @var array
     */
    protected $postVars;


    /**
     * Holds array with get vars from current HTTP request
     *
     * @var array
     */
    protected $getVars;


    /**
     * Holds an array of $_FILES vars
     *
     * @var array
     */
    protected $filesVars;


    /**
     * Holds an merged array of get and post vars with Post Vars precedence
     * @var array
     */
    protected $postGetVars = null;


    /**
     * Holds an merged array of get and post vars with Get Vars precedence
     * @var array
     */
    protected $getPostVars = null;


    /**
     * Holds the extension namespace
     *
     * @var string
     */
    protected $extensionNameSpace;


    /**
     * Constructort, setting extension namespace for adapter
     *
     * @param string $extensionNameSpace Extension namespace to set up adapter for
     */
    public function __construct($extensionNameSpace)
    {
        $this->extensionNameSpace = $extensionNameSpace;
    }


    /**
     * Injects array as post vars
     *
     * @param array $postVars
     */
    public function _injectPostVars(array $postVars = [])
    {
        $this->postVars = $postVars;
        $this->resetMergedVars();
    }


    /**
     * Injects array as get vars
     *
     * @param array $getVars
     */
    public function _injectGetVars(array $getVars = [])
    {
        $this->getVars = $getVars;
        $this->resetMergedVars();
    }


    /**
     * Injects array as files vars ($_FILES)
     *
     * @param array $filesVars
     */
    public function _injectFilesVars(array $filesVars = [])
    {
        $this->filesVars = $filesVars;
    }


    /**
     * Fills a given object with parameters that correspond to namespace identified by object
     *
     * TODO this won't work with DI! Rename in later refacotring.
     *
     * @param GpVarsInjectableInterface $object
     */
    public function injectParametersInObject(GpVarsInjectableInterface $object)
    {
        $object->_injectGPVars($this->extractPgVarsByNamespace($object->getObjectNamespace()));
    }


    /**
     * return parameters by given namespace
     *
     * @param string $namespace
     * @return array
     */
    public function getParametersByNamespace($namespace)
    {
        return $this->extractPgVarsByNamespace($namespace);
    }


    /**
     * return getVares by Namspace
     *
     * @param string $nameSpace
     * @return array
     */
    public function getGetVarsByNamespace($nameSpace)
    {
        return NamespaceUtility::getArrayContentByArrayAndNamespace($this->getVars, $nameSpace);
    }


    /**
     * return postVars by Namespace
     *
     * @param string $nameSpace
     * @return array
     */
    public function getPostVarsByNamespace($nameSpace)
    {
        return NamespaceUtility::getArrayContentByArrayAndNamespace($this->postVars, $nameSpace);
    }


    /**
     * returns filesVars ($_FILES) by Namespace
     *
     * @param string $nameSpace
     * @return array
     */
    public function getFilesVarsByNamespace($nameSpace)
    {
        return NamespaceUtility::getArrayContentByArrayAndNamespace($this->filesVars, $nameSpace);
    }


    /**
     * Extracts merged GP vars for a given namespace. Merges Get vars over Post vars
     *
     * @param string $namespace
     * @return array Merged get and post vars for given namespace
     */
    public function extractGpVarsByNamespace($namespace)
    {
        return NamespaceUtility::getArrayContentByArrayAndNamespace($this->getMergedGpVars(), $namespace);
    }


    /**
     * Extracts merged GP vars for a given namespace. Merges Post vars over Get vars
     *
     * @param string $namespace
     * @return array Merged get and post vars for given namespace
     */
    public function extractPgVarsByNamespace($namespace)
    {
        return NamespaceUtility::getArrayContentByArrayAndNamespace($this->getMergedPgVars(), $namespace);
    }


    /**
     * Merges Post vars over Get vars
     *
     * @return array Merged get and post vars
     */
    protected function getMergedPgVars()
    {
        if (!isset($this->getPostVars) || !is_array($this->postGetVars)) {
            $this->postGetVars = $this->postVars;
            if (is_array($this->getVars) && is_array($this->postVars)) {
                $this->postGetVars = $this->getVars;
                ArrayUtility::mergeRecursiveWithOverrule($this->postGetVars, $this->postVars);
            }
        }

        return $this->postGetVars;
    }


    /**
     * Merges Get vars over Post vars
     *
     * @return array Merged get and post vars
     */
    protected function getMergedGpVars()
    {
        if (!isset($this->getPostVars) || !is_array($this->getPostVars)) {
            $this->getPostVars = $this->getVars;
            if (is_array($this->getVars) && is_array($this->postVars)) {
                $this->getPostVars = $this->postVars;
                ArrayUtility::mergeRecursiveWithOverrule($this->getPostVars, $this->getVars);
            }
        }

        return $this->getPostVars;
    }


    /**
     * @return string
     */
    public function getExtensionNameSpace()
    {
        return $this->extensionNameSpace;
    }


    /**
     * Returns true, if there are no submit values for current extension namespace for current request.
     *
     * If includingFileVars is set to true, return value will be 'false', if there are submitted values
     * in $_FILES
     *
     * @param bool $includingFileVars If set to true, submitted values in $_FILES are also checked
     * @return bool True, if there are no gpvars in current request.
     */
    public function isEmptySubmit($includingFileVars = false)
    {
        if ($includingFileVars && count($this->filesVars) > 0) {
            return false;
        } elseif (count($this->getVars) == 0 && count($this->postVars) == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function resetMergedVars()
    {
        unset($this->getPostVars);
        unset($this->postGetVars);
    }
}
