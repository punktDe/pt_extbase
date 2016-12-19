<?php
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

use TYPO3\CMS\Extbase\Error\Error;

/**
 * Abstract Controller
 *
 * Extend this controller to avoid any exploitable Extbase error messages
 *
 * @package pt_extbase
 * @subpackage Controller
 */
class Tx_PtExtbase_Controller_AbstractApiController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var \PunktDe\PtExtbase\Logger\Logger
     */
    protected $logger;


    /**
     * @param \PunktDe\PtExtbase\Logger\Logger $logger
     * @return void
     */
    public function injectLogger(\PunktDe\PtExtbase\Logger\Logger $logger)
    {
        $this->logger = $logger;
    }



    /**
     * Handles all exceptions thrown inside the application
     */
    protected function callActionMethod()
    {
        try {
            parent::callActionMethod();
        } catch (Exception $exception) {
            if ($exception instanceof \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException) {
                throw $exception;
            }

            header('HTTP/1.1 500 Internal Server Error', true, 500);

            $this->response->setContent($exception->getCode());

            if ($exception instanceof \PunktDe\PtExtbase\Exception\LoggerException) {
                $this->logger->log($exception->getLogLevel(), sprintf('%s (%s)', $exception->getMessage(), $exception->getCode()), get_class($this));
            } else {
                $this->logger->error(sprintf('%s (%s)', $exception->getMessage(), $exception->getCode()), get_class($this));
            }

            $this->objectManager->get((\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class))->persistAll();

            $this->cleanUpAtException($exception);

            return $exception->getCode();
        }
    }



    /**
     * Use this template method in own ApiController to implement further steps if an exception is thrown
     *
     * @param Exception $exception
     */
    protected function cleanUpAtException(Exception $exception)
    {
    }



    /**
     * Handles all errors thrown during the dispatcher / validation phase
     *
     * @return void
     */
    protected function errorAction()
    {
        header('HTTP/1.1 500 Internal Server Error', true, 500);

        $this->handleError($this->findFirstError($this->arguments->getValidationResults()));

        $this->objectManager->get((\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class))->persistAll();

        die();
    }



    /**
     * @param \TYPO3\CMS\Extbase\Error\Result $validationResult
     * @return Error
     */
    protected function findFirstError(\TYPO3\CMS\Extbase\Error\Result $validationResult)
    {
        $error = $validationResult->getFirstError();
        if ($error instanceof Error) {
            return $error;
        }

        foreach ($validationResult->getSubResults() as $argumentName => $subValidationResult) { /** @var $subValidationResult \TYPO3\CMS\Extbase\Error\Result */
            $error = $this->findFirstError($subValidationResult);
            if ($error instanceof Error) {
                return $error;
            }
        }
    }



    /**
     * @param mixed $error
     * @return void
     */
    protected function handleError($error)
    {
        if ($error instanceof Error) {
            $this->logger->error(sprintf('%s (%s)', $error->getMessage(), $error->getCode()));
            echo $error->getCode();
        } else {
            $this->logger->error('Unknown Error while dispatching the controller action. (1400683671)', get_class($this));
            echo 1400683671;
        }
    }
}
