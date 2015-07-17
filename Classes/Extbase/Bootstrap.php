<?php
namespace PunktDe\PtExtbase\Extbase;

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

use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Extbase Bootstrap
 *
 * @package PunktDe\PtExtbase\Extbase
 */
class Bootstrap {

	/**
	 * @var ObjectManagerInterface
	 */
	protected $objectManager;


	/**
	 * @param ObjectManagerInterface $objectManager
	 */
	public function injectObjectManager(ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}



	/**
	 * @param string $extensionName The condensed upper camel case extension key
	 * @param string $pluginName
	 * @return void
	 */
	public function boot($extensionName, $pluginName = 'dummy') {
		$configuration['extensionName'] = $extensionName;
		$configuration['pluginName'] = $pluginName;
		$extbaseBootstrap = $this->objectManager->get('TYPO3\CMS\Extbase\Core\Bootstrap'); /** @var \TYPO3\CMS\Extbase\Core\Bootstrap $extbaseBootstrap  */
		$extbaseBootstrap->initialize($configuration);
	}

}
