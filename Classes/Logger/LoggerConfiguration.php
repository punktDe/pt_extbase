<?php
namespace PunktDe\PtExtbase\Logger;

/***************************************************************
 *  Copyright (C) 2014 punkt.de GmbH
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

use Neos\Utility\Files;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\SingletonInterface;
use PunktDe\PtExtbase\Div;

/**
 * Logger Configuration
 */
class LoggerConfiguration implements SingletonInterface
{
    /**
     * @var array
     */
    protected $extensionConfiguration = [];


    /**
     * @var string
     */
    protected $logFilePath;


    /**
     * @var string
     */
    protected $exceptionDirectory;


    /**
     * @var integer
     */
    protected $logLevelThreshold = 6;

    public function __construct()
    {
        $this->extensionConfiguration = Div::returnExtConfArray('pt_extbase');
        $this->evaluateLogFilePath();
        $this->evaluateExceptionDirectory();
        $this->setLogLevelThresholdByExtensionConfigurationProperty('logLevelThreshold');
    }



    /**
     * @return void
     */
    protected function evaluateLogFilePath()
    {
        if (array_key_exists('logFilePath', $this->extensionConfiguration)) {
            $this->logFilePath = $this->extensionConfiguration['logFilePath'];
        } else {
            $this->logFilePath = Files::concatenatePaths([PATH_site, '/typo3temp/application.log']);
        }

        if (!file_exists($this->logFilePath)) {
            touch($this->logFilePath);
        }
    }



    /**
     * @return void
     */
    protected function evaluateExceptionDirectory()
    {
        if (!$this->exceptionDirectory) {
            $path_parts = pathinfo($this->logFilePath);

            $this->exceptionDirectory = Files::concatenatePaths([realpath($path_parts['dirname']), 'Exceptions']);
            Files::createDirectoryRecursively($this->exceptionDirectory);
        }
    }



    /**
     * @param string $property
     * @return integer|NULL
     */
    protected function setLogLevelThresholdByExtensionConfigurationProperty($property)
    {
        if (array_key_exists($property, $this->extensionConfiguration)) {
            LogLevel::validateLevel($this->extensionConfiguration[$property]);
            $this->$property =  (integer) $this->extensionConfiguration[$property];
        }
    }

    /**
     * @return string
     */
    public function getExceptionDirectory()
    {
        return $this->exceptionDirectory;
    }

    /**
     * @return string
     */
    public function getLogFilePath()
    {
        return $this->logFilePath;
    }

    /**
     * @return int
     */
    public function getLogLevelThreshold()
    {
        return $this->logLevelThreshold;
    }
}
