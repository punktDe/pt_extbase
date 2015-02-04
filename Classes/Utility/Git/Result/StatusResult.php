<?php
namespace PunktDe\PtExtbase\Utility\Git\Result;

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

/**
 * Path Status
 *
 * @package PunktDe\PtExtbase\Utility\Git\Result
 */
class StatusResult extends AbstractResult {

	/**
	 * @const SHORT_LOG_LINE_PATTERN
	 */
	const SHORT_LOG_LINE_PATTERN = '/^(?<indexStatus>.)(?<worktreeStatus>.)[[:space:]]"?(?<path>.+?)"?(?:(?:[[:space:]]->[[:space:]])"?(?<correspondingPath>.+?)"?)?$/';


	/**
	 * Build the result
	 *
	 * Despite its totally weird API we use strtok() here due to performance issues.
	 *
	 * @return void
	 */
	protected function buildResult() {
		$separator = "\n";
		$line = strtok($this->rawResult, $separator);

		while ($line !== FALSE) {
			preg_match(self::SHORT_LOG_LINE_PATTERN, $line, $statusElements);

			$pathStatus = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Result\PathStatus'); /** @var \PunktDe\PtExtbase\Utility\Git\Result\PathStatus $pathStatus */
			$pathStatus->setIndexStatus(trim($statusElements['indexStatus']));
			$pathStatus->setWorkTreeStatus(trim($statusElements['worktreeStatus']));
			$pathStatus->setPath(trim($statusElements['path']));
			$pathStatus->setCorrespondingPath(trim($statusElements['correspondingPath']));
			$this->result->attach($pathStatus);

			$line = strtok("\n");
		}
	}

}
