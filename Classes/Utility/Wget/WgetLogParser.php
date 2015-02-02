<?php
 /***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Daniel Lienert <lienert@punkt.de>
 *
 *
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

namespace PunktDe\PtExtbase\Utility\Wget;


class WgetLogParser {


	/**
	 * @var WgetCommand
	 */
	protected $wgetCommand;


	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;



	public function parseLog(WgetCommand $wgetCommand) {
		$this->checkAndSetWgetCommand($wgetCommand);

		return $this->buildLogFileEntryArray($this->readLogFileContent());
	}



	/**
	 * @param WgetCommand $wgetCommand
	 * @throws \Exception
	 */
	protected function checkAndSetWgetCommand(WgetCommand $wgetCommand) {
		if($wgetCommand->isNoVerbose() === FALSE) {
			throw new \Exception('The wget command was not set to no-verbose, which is needed to parse the log file.', 1422353522);
		}

		if($wgetCommand->getOutputFile() === '') {
			throw new \Exception('The output file was not set for the wget command which is needed to parse the log file.', 1422353523);
		}

		$this->wgetCommand = $wgetCommand;
	}


	/**
	 * @return string
	 * @throws \Exception
	 */
	protected function readLogFileContent() {
		if(!file_exists($this->wgetCommand->getOutputFile()) || !is_readable($this->wgetCommand->getOutputFile())) {
			throw new \Exception(sprintf('The log file %s could not be read.', $this->wgetCommand->getOutputFile()), 1422438059);
		}

		return trim(file_get_contents($this->wgetCommand->getOutputFile()));
	}


	/**
	 * @param $logFileContent
	 * @return WgetLog
	 */
	protected function buildLogFileEntryArray($logFileContent) {
		$structuredLogFileEntries = $this->splitLogFileEntries($logFileContent);

		$wgetLog = $this->objectManager->get('\PunktDe\PtExtbase\Utility\Wget\WgetLog'); /** @var \PunktDe\PtExtbase\Utility\Wget\WgetLog  $wgetLog */

		foreach($structuredLogFileEntries as $structuredLogFileEntry) {
			$wgetLog->addLogEntry($this->buildLogFileEntry($structuredLogFileEntry));
		}

		return $wgetLog;
	}


	/**
	 * @param $structuredLogEntry
	 * @return WgetLogEntry
	 */
	protected function buildLogFileEntry($structuredLogEntry) {
		$wgetLogEntry = $this->objectManager->get('PunktDe\PtExtbase\Utility\Wget\WgetLogEntry'); /** @var WgetLogEntry $wgetLogEntry  */

		preg_match('/(^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2})/', $structuredLogEntry['header'], $matches);
		$wgetLogEntry->setFetchDate(\DateTime::createFromFormat('Y-m-d H:i:s', $matches[0]));

		if(preg_match('/URL:(?<url>\S+)/', $structuredLogEntry['header'], $matches) == 0) {
			// When an error occurs, the URL is written to the 'body', so try to find it there
			preg_match('/(?<url>https?:\/\/\S+):/', $structuredLogEntry['body'], $matches);
		}
		$wgetLogEntry->setUrl($matches['url']);

		preg_match('/HTTP\/[0-9,\.]+\s(?<status>\d+).*/', $structuredLogEntry['body'], $matches);
		$wgetLogEntry->setStatus((int) $matches['status']);

		preg_match('/Content-Type:\s(?<contentType>.*)/', $structuredLogEntry['body'], $matches);
		$wgetLogEntry->setContentType($matches['contentType']);

		preg_match('/Content-Length:\s(?<contentLength>.*)/', $structuredLogEntry['body'], $matches);
		$wgetLogEntry->setContentLength((int) $matches['contentLength']);

		return $wgetLogEntry;
	}




	/**
	 * @param $logFileContent
	 * @return array
	 */
	protected function splitLogFileEntries($logFileContent) {
		$logEntryArray =  preg_split('/([0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}.*)/', $logFileContent, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		$structuredLogEntryArray = array();

		foreach($logEntryArray as $key => $entry) {
			if($key % 2 === 0) {
				$structuredLogEntryArray[$key]['body'] = trim($entry);
			} else {
				$structuredLogEntryArray[$key-1]['header'] = trim($entry);
			}
		}

		return $structuredLogEntryArray;
	}
} 