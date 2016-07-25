<?php
namespace PunktDe\PtExtbase\Domain\UserFunctions;

/***************************************************************
 *  Copyright (C) 2015 punkt.de GmbH
 *  Authors: el_equipo <opiuqe_le@punkt.de>
 *
 *  This script is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ExtensionConfigurationReader
 *
 * @package PunktDe\PtExtbase\Domain\UserFunctions
 */
class ExtensionConfigurationReader implements SingletonInterface
{
    /**
     * @var \PunktDe\PtExtbase\Utility\ExtensionSettings
     */
    protected $extensionSettings;

    /**
     * @param string $content
     * @param array $conf
     *
     * @return string
     * @throws \Exception
     */
    public function getValueFromExtensionSettings($content = '', $conf = array())
    {
        $this->extensionSettings = GeneralUtility::makeInstance(\PunktDe\PtExtbase\Utility\ExtensionSettings::class);


        $conf = $conf['userFunc.'];

        $extensionName = $conf['extensionName'];
        $settingName = $conf['settingName'];

        if (!$settingName || !$extensionName) {
            throw new \Exception('You need to provide extensionName and settingName in the userFunc settings!', 1439383004);
        }

        return $this->extensionSettings->getValueFromExtensionSettingsByKey($extensionName, $settingName);
    }
}
