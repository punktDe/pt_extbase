<?php
namespace PunktDe\PtExtbase\Controller;

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
class AbstractApiController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
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
        } catch (\Exception $exception) {
            if (!($exception instanceof \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException)) {
                $this->cleanUpAtException($exception);
            }

            throw $exception;
        }
    }


    /**
     * Use this template method in own ApiController to implement further steps if an exception is thrown
     *
     * @param \Exception $exception
     */
    protected function cleanUpAtException(\Exception $exception)
    {
    }


    /**
     * Handles all errors thrown during the dispatcher / validation phase
     *
     * @return void
     */
    protected function errorAction()
    {
        $error = $this->findFirstError($this->arguments->getValidationResults());

        if (!($error instanceof Error)) {
            $error = new Error('Unknown Error while dispatching the controller action.', 1400683671);
        }

        $this->logger->error(sprintf('%s (%s)', $error->getMessage(), $error->getCode()), get_class($this));

        throw new \TYPO3\CMS\Core\Error\Http\StatusException([\TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_500],
            $error->getMessage(), '', $error->getCode());
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

        foreach ($validationResult->getSubResults() as $argumentName => $subValidationResult) {
            /** @var $subValidationResult \TYPO3\CMS\Extbase\Error\Result */
            $error = $this->findFirstError($subValidationResult);
            if ($error instanceof Error) {
                return $error;
            }
        }

        return null;
    }
}
