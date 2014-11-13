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

/**
 * Abstract Controller
 *
 * Extend this controller to avoid any exploitable Extbase error messages
 *
 * @package pt_dppp_base
 * @subpackage Controller
 */
class Tx_PtExtbase_Controller_AbstractApiController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * @var Tx_PtExtbase_Logger_Logger
	 */
	protected $logger;


	/**
	 * @param Tx_PtExtbase_Logger_Logger $logger
	 *
	 * @return void
	 */
	public function injectLogger(Tx_PtExtbase_Logger_Logger $logger) {
		$this->logger = $logger;
	}


	/**
	 * Handles all exceptions thrown inside the application
	 */
	protected function callActionMethod() {

		try {
			parent::callActionMethod();
		} catch (Exception $exception) {
			header('HTTP/1.1 500 Internal Server Error', TRUE, 500);

			$this->response->setContent($exception->getCode());

			if ($exception instanceof Tx_PtExtbase_Exception_LoggerException) {
				$this->logger->log($exception->getLogLevel(), sprintf('%s (%s)', $exception->getMessage(), $exception->getCode()));
			} else {
				$this->logger->error(sprintf('%s (%s)', $exception->getMessage(), $exception->getCode()));
			}

			$this->objectManager->get('Tx_Extbase_Persistence_Manager')->persistAll();

			return $exception->getCode();
		}
	}


	/**
	 * Handles all errors thrown during the dispatcher / validation phase
	 */
	protected function errorAction() {
		header('HTTP/1.1 500 Internal Server Error', TRUE, 500);

		$error = NULL;
		$validationResult = $this->arguments->getValidationResults();

		foreach ($validationResult->getSubResults() as $argumentName => $subValidationResult) {
			/** @var $subValidationResult Tx_Extbase_Error_Result */
			$error = $subValidationResult->getFirstError();
			if ($error instanceof Tx_Extbase_Error_Error) {
				break;
			}
		}

		$this->handleError($error);

		$this->objectManager->get('Tx_Extbase_Persistence_Manager')->persistAll();

		die();
	}


	/**
	 * @param mixed $error
	 *
	 * @return void
	 */
	protected function handleError($error) {
		if ($error instanceof Tx_Extbase_Error_Error) {
			$this->logger->error(sprintf('%s (%s)', $error->getMessage(), $error->getCode()));
			echo $error->getCode();
		} else {
			$this->logger->error('Unknown Error while dispatching the controller action. (1400683671)');
			echo 1400683671;
		}
	}
}
