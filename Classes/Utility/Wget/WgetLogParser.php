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


	public function parseLog(WgetCommand $wgetCommand) {
		$this->checkAndSetWgetCommand($wgetCommand);
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
			throw new \Exception('The output file was not set for the wget commadn which is needed to parse the log file.', 1422353523);
		}

		$this->wgetCommand = $wgetCommand;
	}


	protected function readLogFileContent() {

	}


	protected function splitLogFileEntries($logFileContent) {

	}

	protected function generateLogFileEntries() {

	}
} 