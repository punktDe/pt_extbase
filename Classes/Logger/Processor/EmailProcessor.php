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

use \TYPO3\CMS\Fluid\View\StandaloneView;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Log\LogRecord;

/**
 * Logger Email Processor
 */
class Tx_PtExtbase_Logger_Processor_EmailProcessor extends TYPO3\CMS\Core\Log\Processor\AbstractProcessor {


	/**
	 * @var string
	 */
	protected $receivers;


	/**
	 * @var LogRecord
	 */
	protected $logRecord;


	/**
	 * @var Tx_PtExtbase_Utility_ServerInformation
	 */
	protected $serverInformation;


	/**
	 * @var \PunktDe\PtExtbase\Utility\RequestInformation
	 */
	protected $requestInformation;


	/**
	 * @var Tx_PtExtbase_Utility_UserAgent
	 */
	protected $userAgent;


	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 */
	protected $objectManager;


	/**
	 * @param array $options
	 */
	public function __construct(array $options = array()) {
		parent::__construct($options);
		$this->objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
		$this->serverInformation = $this->objectManager->get('Tx_PtExtbase_Utility_ServerInformation');
		$this->requestInformation = $this->objectManager->get('PunktDe\PtExtbase\Utility\RequestInformation');
		$this->userAgent = $this->objectManager->get('Tx_PtExtbase_Utility_UserAgent');
	}



	/**
	 * @param LogRecord $logRecord
	 * @return LogRecord
	 */
	public function processLogRecord(LogRecord $logRecord) {
		$this->logRecord = $logRecord;

		try {
			$mail = $this->objectManager->get('TYPO3\CMS\Core\Mail\MailMessage');
			/** @var \TYPO3\CMS\Core\Mail\MailMessage $mail */
			$mail->setFrom(array("noreply@punkt.de" => "noreply@punkt.de"));
			$mail->setTo($this->receivers);
			$mail->setSubject(sprintf('%s: Error on system %s', $this->getNamespaceOfLogComponent(), $this->serverInformation->getServerHostName()));
			$mail->setBody($this->renderViewForMail());
			$mail->send();
		} catch (\Exception $exception) {
			error_log('The error mail could not be sent!');
		}
		return $logRecord;
	}



	/**
	 * @return string
	 */
	protected function renderViewForMail() {
		$view = new StandaloneView();
		$view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('EXT:pt_extbase/Resources/Private/Templates/Logger/ErrorEmail.html'));
		$view->assign('logRecord', $this->logRecord);
		$view->assign('serverInformation', $this->serverInformation);
		$view->assign('userAgent', $this->userAgent);
		$view->assign('requestId', $this->requestInformation->getCurrentRequestId());
		return $view->render();
	}


	/**
	 * @param string $receivers
	 */
	public function setReceivers($receivers) {
		$this->receivers = $receivers;
	}

	/**
	 * @return string
	 */
	protected function getNamespaceOfLogComponent() {
		$componentParts = explode('.', $this->logRecord->getComponent());

		return $componentParts[1];
	}

}