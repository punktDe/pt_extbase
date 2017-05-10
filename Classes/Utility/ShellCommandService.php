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
class ShellCommandService implements SingletonInterface
{
    /**
     * @inject
     * @var \PunktDe\PtExtbase\Logger\Logger
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
     * @var string
     */
    protected $command = '';


    /**
     * @var integer
     */
    protected $exitCode = 0;


    /**
     * @var string
     */
    protected $returnedOutput = '';


    /**
     * @var boolean
     */
    protected $redirectStandardErrorToStandardOut = false;


    /**
     * @var boolean
     */
    protected $simulateMode = false;


    /**
     * @param mixed $command The shell command to execute, either string or array of commands
     * @return mixed The output of the shell command or FALSE if the command returned a non-zero exit code and $ignoreErrors was enabled.
     */
    public function execute($command)
    {
        $this->command = $command;
        if ($this->simulateMode === true) {
            return $this->returnedOutput;
        }
        return $this->executeCommand($command);
    }



    /**
     * @param string $command
     * @return string
     */
    protected function executeCommand($command)
    {
        if ($this->hostname === 'localhost') {
            list($this->exitCode, $this->returnedOutput) = $this->executeLocalCommand();
        } else {
            list($this->exitCode, $this->returnedOutput) = $this->executeRemoteCommand();
        }
        $this->checkResult();
        return $this->returnedOutput;
    }



    /**
     * @return array
     */
    protected function executeLocalCommand()
    {
        return $this->executeProcess($this->command);
    }



    /**
     * @return mixed The output of the shell command or FALSE if the command returned a non-zero exit code
     */
    protected function executeRemoteCommand()
    {
        $sshOptions = [];
        $sshCommand = 'ssh ' . implode(' ', $sshOptions) . ' ' . escapeshellarg($this->username . '@' . $this->hostname) . ' ' . escapeshellarg($this->command) . ' 2>&1';
        return $this->executeProcess($sshCommand);
    }



    /**
     * Open a process with popen and process each line by logging and
     * collecting its output.
     *
     * @param string $command
     * @param string $logPrefix
     * @return array The exit code of the command and the returned output
     */
    protected function executeProcess($command, $logPrefix = '')
    {
        $returnedOutput = '';
        $fp = popen($this->prepareCommand($command), 'r');

        $this->logger->info('executing command: ' . $command);
        while (($line = fgets($fp)) !== false) {
            $this->logger->debug($logPrefix . rtrim($line), __CLASS__);
            $returnedOutput .= $line;
        }
        $exitCode = pclose($fp);

        return [$exitCode, $returnedOutput];
    }



    /**
     * @param string $command
     * @return string
     */
    protected function prepareCommand($command)
    {
        if ($this->redirectStandardErrorToStandardOut) {
            return sprintf("%s 2>&1", $command);
        }
        return $command;
    }



    /**
     * @return void
     */
    protected function checkResult()
    {
        if ($this->exitCode !== 0) {
            $this->logger->error(sprintf("Shell command \"%s\" returned exist status %s", $this->command, $this->exitCode), __CLASS__);
        }
    }



    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }



    /**
     * @param string $hostname
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }



    /**
     * @param boolean $redirectStandardErrorToStandardOut
     */
    public function setRedirectStandardErrorToStandardOut($redirectStandardErrorToStandardOut)
    {
        $this->redirectStandardErrorToStandardOut = $redirectStandardErrorToStandardOut;
    }



    /**
     * @return integer
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }



    /**
     * @param boolean $simulateMode
     */
    public function setSimulateMode($simulateMode)
    {
        $this->simulateMode = $simulateMode;
    }



    /**
     * Used for tests
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }



    /**
     * Used for tests
     *
     * @param integer $simulatedExitCode
     */
    public function setSimulatedExitCode($simulatedExitCode)
    {
        $this->exitCode = $simulatedExitCode;
    }



    /**
     * Used for tests
     *
     * @param string $simulatedReturnedOutput
     */
    public function setSimulatedReturnedOutput($simulatedReturnedOutput)
    {
        $this->returnedOutput = $simulatedReturnedOutput;
    }
}
