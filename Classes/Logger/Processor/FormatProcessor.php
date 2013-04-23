<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012-2013 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
*  Authors: Daniel Lienert
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

class Tx_PtExtbase_Logger_FormatProcessor extends t3lib_log_processor_Abstract {


	protected $formatTokens = array(
		'.h1' => 'formatH1',
		'.h2' => 'formatH2',
		'.h3' => 'formatH3',
		'.h5' => 'formatH5',
	);


	/**
	 * Formats the log
	 *
	 * @param t3lib_log_Record $logRecord
	 * @return t3lib_log_Record
	 */
	public function processLogRecord(t3lib_log_Record $logRecord) {
		$message = $logRecord->getMessage();

		foreach($this->formatTokens as $token => $formattingMethod) {
			if(substr($message,0,strlen($token)) == $token) {
				$message = substr($message, strlen($token));
				$message = $this->$formattingMethod(trim($message));
			}
		}

		$logRecord->setMessage($message);
		return $logRecord;
	}



	/**
	 * @param $message
	 * @return string
	 */
	protected function formatH1($message) {
		return "\n" . str_pad('', strlen($message),'=') . "\n" . $message . "\n" . str_pad('', strlen($message),'=');
	}


	/**
	 * @param $message
	 * @return string
	 */
	protected function formatH2($message) {
		return "\n" . str_pad('', strlen($message),'-') . "\n" . $message . "\n" . str_pad('', strlen($message),'-');
	}


	/**
	 * @param $message
	 * @return string
	 */
	protected function formatH3($message) {
		return "\n"  . $message . "\n" . str_pad('', strlen($message),'-') ;
	}


	/**
	 * @param $message
	 * @return string
	 */
	protected function formatH5($message) {
		return "== " . $message . " ==" ;
	}
}