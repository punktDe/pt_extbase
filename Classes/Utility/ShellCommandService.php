<?php
namespace PunktDe\PtExtbase\Utility;

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

use TYPO3\CMS\Core\SingletonInterface;

/**
 * Shell Command Service
 *
 * @package \PunktDe\PtExtbase\Utility
 */
class ShellCommandService implements SingletonInterface {

	/**
	 * @inject
	 * @var \Tx_PtExtbase_Logger_Logger
	 */
	protected $logger;


	/**
	 * @var string
	 */
	protected $username = '';


	/**
	 * @var string
	 */
	protected $hostname = 'localhost';



	/**
	 * @param string $username
	 */
	public function setUsername($username) {
		$this->username = $username;
	}



	/**
	 * @param string $hostname
	 */
	public function setHostname($hostname) {
		$this->hostname = $hostname;
	}



	/**
	 * @param mixed $command The shell command to execute, either string or array of commands
	 * @return mixed The output of the shell command or FALSE if the command returned a non-zero exit code and $ignoreErrors was enabled.
	 */
	public function execute($command) {
		if ($this->hostname === 'localhost') {
			list($exitCode, $returnedOutput) = $this->executeLocalCommand($command);
		} else {
			list($exitCode, $returnedOutput) = $this->executeRemoteCommand($command);
		}
		return ($exitCode === 0 ? $returnedOutput : FALSE);
	}



	/**
	 * @param string $command
	 * @return array
	 */
	protected function executeLocalCommand($command) {
		$returnedOutput = '';
		$fp = popen($command, 'r');
		while (($line = fgets($fp)) !== FALSE) {
			$this->logger->info('> ' . $line, __CLASS__);
			$returnedOutput .= $line;
		}
		$exitCode = pclose($fp);
		return array($exitCode, $returnedOutput);
	}



	/**
	 * @param string $command
	 * @return mixed The output of the shell command or FALSE if the command returned a non-zero exit code
	 */
	public function executeRemoteCommand($command) {
		$sshOptions = array();
		$sshCommand = 'ssh ' . implode(' ', $sshOptions) . ' ' . escapeshellarg($this->username . '@' . $this->hostname) . ' ' . escapeshellarg($command) . ' 2>&1';
		return $this->executeProcess($sshCommand, '    > ');
	}



	/**
	 * Open a process with popen and process each line by logging and
	 * collecting its output.
	 *
	 * @param string $command
	 * @param string $logPrefix
	 * @return array The exit code of the command and the returned output
	 */
	public function executeProcess($command, $logPrefix) {
		$returnedOutput = '';
		$fp = popen($command, 'r');
		while (($line = fgets($fp)) !== FALSE) {
			$this->logger->info($logPrefix . rtrim($line), __CLASS__);
			$returnedOutput .= $line;
		}
		$exitCode = pclose($fp);
		return array($exitCode, trim($returnedOutput));
	}

}
