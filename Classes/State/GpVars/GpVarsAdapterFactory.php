<?php
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

/**
 * Class implements a factory for GET/POST Var Adapter.
 *
 * @package State
 * @subpackage GpVars
 */
class Tx_PtExtbase_State_GpVars_GpVarsAdapterFactory implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * Singleton instances of GET/POST Var Adapters.
     * There is one gpVarsAdapter for each extensionNamespace
     *
     * @var array<Tx_PtExtbase_State_GpVars_GpVarsAdapter>
     */
    private $instances = [];
    
    
    
    /**
     * Factory method for GET/POST Var Adapter.
     * 
     * @param string $extensionNameSpace 
     * @return Tx_PtExtbase_State_GpVars_GpVarsAdapter Singleton instance of GET/POST Var Adapter.
     */
    public function getInstance($extensionNameSpace)
    {
        if (!array_key_exists($extensionNameSpace, $this->instances) || $this->instances[$extensionNameSpace] == null) {
            $this->instances[$extensionNameSpace] = new Tx_PtExtbase_State_GpVars_GpVarsAdapter($extensionNameSpace);
            $this->instances[$extensionNameSpace]->_injectGetVars($this->extractExtensionVariables($_GET, $extensionNameSpace));
            $this->instances[$extensionNameSpace]->_injectPostVars($this->extractExtensionVariables($_POST, $extensionNameSpace));
            $this->instances[$extensionNameSpace]->_injectFilesVars($this->extractExtensionVariables($_FILES, $extensionNameSpace));
        }
    
        return $this->instances[$extensionNameSpace];
    }



    /**
     * Remove the extension name from the variables
     *
     * @param array $vars
     * @param string $extensionNameSpace
     * @return array
     */
    protected function extractExtensionVariables($vars, $extensionNameSpace)
    {
        $extractedVars = $vars[$extensionNameSpace];
        if (!is_array($extractedVars)) {
            $extractedVars = [];
        }
        
        return $extractedVars;
    }
}
