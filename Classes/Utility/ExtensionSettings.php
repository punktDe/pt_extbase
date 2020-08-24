<?php
namespace PunktDe\PtExtbase\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 punkt.de GmbH
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

use PunktDe\PtExtbase\Div;
use PunktDe\PtExtbase\Exception\Exception as ExtbaseException;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Extension Settings
 */
class ExtensionSettings implements SingletonInterface
{
    /**
     * @var array
     */
    protected $extensionSettings = [];

    /**
     * @param string $extensionKey
     * @return array
     * @throws ExtbaseException
     */
    public function getExtensionSettings($extensionKey)
    {
        $this->cacheExtensionSettings($extensionKey);
        return $this->extensionSettings[$extensionKey];
    }

    /**
     * @param string $extensionKey
     * @param string $key
     * @return string
     * @throws \Exception
     */
    public function getValueFromExtensionSettingsByKey($extensionKey, $key)
    {
        $settings = $this->getExtensionSettings($extensionKey);
        if (!isset($settings[$key])) {
            throw new \Exception('No key ' . $key . ' set in extension ' . $extensionKey . '! 1334406600');
        }
        return $settings[$key];
    }

    /**
     * @param string $extensionKey
     * @param string $key
     * @return string
     * @throws \Exception
     * @deprecated
     */
    public function getKeyFromExtensionSettings($extensionKey, $key)
    {
        return $this->getValueFromExtensionSettingsByKey($extensionKey, $key);
    }

    /**
     * @param string $extensionKey
     * @return array
     * @throws ExtbaseException
     */
    protected function cacheExtensionSettings($extensionKey)
    {
        if (!array_key_exists($extensionKey, $this->extensionSettings) || !is_array($this->extensionSettings[$extensionKey])) {
            $this->extensionSettings[$extensionKey] = [];
            $this->extensionSettings[$extensionKey] = $this->loadExtensionSettings($extensionKey);
        }
    }

    /**
     * @param string $extensionKey
     * @return array
     * @throws ExtbaseException
     */
    protected function loadExtensionSettings($extensionKey)
    {
        $settings = Div::returnExtConfArray($extensionKey);
        return $settings;
    }

    /**
     * Use for testing purposes
     *
     * @param array $extensionSettings
     */
    public function overrideExtensionSettings(array $extensionSettings)
    {
        $this->extensionSettings = $extensionSettings;
    }
}
