<?php
namespace PunktDe\PtExtbase\Logger;

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

use PunktDe\PtExtbase\Logger\Processor\ReplaceComponentProcessor;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Log\LogLevel;

/**
 * A Logger
 */
class Logger implements SingletonInterface
{
    /**
     * @var LoggerManager
     */
    protected $loggerManager;


    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;


    /**
     * @var string
     */
    protected $logFilePath;


    /**
     * @var string
     */
    protected $exceptionDirectory;


    /**
     * @inject
     * @var \PunktDe\PtExtbase\Logger\LoggerConfiguration
     */
    protected $loggerConfiguration;


    /**
     * @var string
     */
    protected $defaultLogComponent;

    
    public function __construct()
    {
        $this->defaultLogComponent = __CLASS__;
    }

    /**
     * @param LoggerManager $loggerManager
     */
    public function injectLoggerManager(LoggerManager $loggerManager)
    {
        $this->loggerManager = $loggerManager;
    }

    /**
     * @return void
     */
    public function initializeObject()
    {
        $this->configureLogger();
    }



    /**
     * @param string $logFilePath
     * @param string $exceptionDirectory
     * @return void
     */
    public function configureLogger($logFilePath = '', $exceptionDirectory = '')
    {
        $this->logFilePath = empty($logFilePath) ? $this->loggerConfiguration->getLogFilePath() : $logFilePath;
        $this->exceptionDirectory = empty($exceptionDirectory) ? $this->loggerConfiguration->getExceptionDirectory() : $exceptionDirectory;
        $this->configureLoggerProperties();
    }



    /**
     * @return void
     */
    protected function configureLoggerProperties()
    {
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['writerConfiguration'] = array(
            $this->loggerConfiguration->getLogLevelThreshold() => array(
                'Tx_PtExtbase_Logger_Writer_FileWriter' => array(
                    'logFile' => $this->logFilePath
                )
            ),
        );

        $GLOBALS['TYPO3_CONF_VARS']['LOG']['processorConfiguration'][LogLevel::DEBUG] = array(
            ReplaceComponentProcessor::class => []
        );

        if ($this->loggerConfiguration->weHaveAnyEmailReceivers()) {
            $GLOBALS['TYPO3_CONF_VARS']['LOG']['processorConfiguration'][$this->loggerConfiguration->getEmailLogLevelThreshold()]['Tx_PtExtbase_Logger_Processor_EmailProcessor'] = array(
                'receivers' => $this->loggerConfiguration->getEmailReceivers()
            );
        }
    }



    /**
     * @param string $logComponent
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger($logComponent)
    {
        if ($logComponent === null) {
            $logComponent = $this->defaultLogComponent;
        }
        return $this->logger = $this->loggerManager->getLogger($logComponent);
    }



    /**
     * Shortcut to log a EMERGENCY record.
     *
     * @param string $message Log message.
     * @param array $data Additional data to log
     * @param string $logComponent
     */
    public function emergency($message, $logComponent = null, array $data = [])
    {
        $this
            ->enrichLogDataByComponent($data, $logComponent)
            ->getLogger($logComponent)->emergency($message, $data);
    }



    /**
     * Shortcut to log a ALERT record.
     *
     * @param string $message Log message.
     * @param array $data Additional data to log
     * @param string $logComponent
     */
    public function alert($message, $logComponent = null, array $data = [])
    {
        $this
            ->enrichLogDataByComponent($data, $logComponent)
            ->getLogger($logComponent)->alert($message, $data);
    }



    /**
     * Shortcut to log a CRITICAL record.
     *
     * @param string $message Log message.
     * @param array $data Additional data to log
     * @param string $logComponent
     */
    public function critical($message, $logComponent = null, array $data = [])
    {
        $this
            ->enrichLogDataByComponent($data, $logComponent)
            ->getLogger($logComponent)->critical($message, $data);
    }



    /**
     * Shortcut to log an ERROR record.
     *
     * @param string $message Log message.
     * @param array $data Additional data to log
     * @param string $logComponent
     */
    public function error($message, $logComponent = null, array $data = [])
    {
        $this
            ->enrichLogDataByComponent($data, $logComponent)
            ->getLogger($logComponent)->error($message, $data);
    }



    /**
     * Shortcut to log an WARN record.
     *
     * @param string $message Log message.
     * @param array $data Additional data to log
     * @param string $logComponent
     */
    public function warning($message, $logComponent = null, array $data = [])
    {
        $this
            ->enrichLogDataByComponent($data, $logComponent)
            ->getLogger($logComponent)->warning($message, $data);
    }



    /**
     * Shortcut to log an NOTICE record.
     *
     * @param string $message Log message.
     * @param array $data Additional data to log
     * @param string $logComponent
     */
    public function notice($message, $logComponent = null, array $data = [])
    {
        $this
            ->enrichLogDataByComponent($data, $logComponent)
            ->getLogger($logComponent)->notice($message, $data);
    }



    /**
     * Shortcut to log an INFORMATION record.
     *
     * @param string $message Log message.
     * @param array $data Additional data to log
     * @param string $logComponent
     */
    public function info($message, $logComponent = null, array $data = [])
    {
        $this
            ->enrichLogDataByComponent($data, $logComponent)
            ->getLogger($logComponent)->info($message, $data);
    }



    /**
     * Shortcut to log a DEBUG record.
     *
     * @param string $message Log message.
     * @param array $data Additional data to log
     * @param string $logComponent
     */
    public function debug($message, $logComponent = null, array $data = [])
    {
        $this
            ->enrichLogDataByComponent($data, $logComponent)
            ->getLogger($logComponent)->debug($message, $data);
    }



    /**
     * @param integer $level
     * @param string $message
     * @param array $data
     * @param string $logComponent
     */
    public function log($level, $message, $logComponent = null, array $data = [])
    {
        $this
            ->enrichLogDataByComponent($data, $logComponent)
            ->getLogger($logComponent)->log($level, $message, $data);
    }



    /**
     * Writes information about the given exception into the log.
     *
     * @param \Exception $exception The exception to log
     * @param string $logComponent
     * @param array $additionalData Additional data to log
     * @return void
     * @api
     */
    public function logException(\Exception $exception, $logComponent = null, array $additionalData = [])
    {
        $backTrace = $exception->getTrace();
        $message = $this->getExceptionLogMessage($exception);

        if ($exception->getPrevious() !== null) {
            $additionalData['previousException'] = $this->getExceptionLogMessage($exception->getPrevious());
        }

        if (!file_exists($this->exceptionDirectory)) {
            mkdir($this->exceptionDirectory);
        }

        if (file_exists($this->exceptionDirectory) && is_dir($this->exceptionDirectory) && is_writable($this->exceptionDirectory)) {
            $referenceCode = ($exception->getCode() > 0 ? $exception->getCode() . '.' : '') . date('YmdHis', $_SERVER['REQUEST_TIME']) . substr(md5(rand()), 0, 6);
            $exceptionDumpPathAndFilename = \Tx_PtExtbase_Utility_Files::concatenatePaths(array($this->exceptionDirectory,  $referenceCode . '.txt'));
            file_put_contents($exceptionDumpPathAndFilename, $message . PHP_EOL . PHP_EOL . $this->getBacktraceCode($backTrace, 1));
            $message .= ' - See also: ' . basename($exceptionDumpPathAndFilename);
        } else {
            $this->warning(sprintf('Could not write exception backtrace into %s because the directory could not be created or is not writable.', $this->exceptionDirectory), $logComponent, []);
        }

        $this->critical($message, $logComponent, $additionalData);
    }



    /**
     * @param \Exception $exception
     * @return string
     */
    protected function getExceptionLogMessage(\Exception $exception)
    {
        $exceptionCodeNumber = ($exception->getCode() > 0) ? ' #' . $exception->getCode() : '';
        $backTrace = $exception->getTrace();
        $line = isset($backTrace[0]['line']) ? ' in line ' . $backTrace[0]['line'] . ' of ' . $backTrace[0]['file'] : '';
        return 'Uncaught exception' . $exceptionCodeNumber . $line . ': ' . $exception->getMessage();
    }



    /**
     * Renders some backtrace
     *
     * @param array $trace The trace
     * @return string Backtrace information
     */
    protected function getBacktraceCode(array $trace)
    {
        $backtraceCode = '';
        if (count($trace)) {
            foreach ($trace as $index => $step) {
                $class = isset($step['class']) ? $step['class'] . '::' : '';

                $arguments = '';
                if (isset($step['args']) && is_array($step['args'])) {
                    foreach ($step['args'] as $argument) {
                        $arguments .= (strlen($arguments) === 0) ? '' : ', ';
                        if (is_object($argument)) {
                            $arguments .= get_class($argument);
                        } elseif (is_string($argument)) {
                            $arguments .= '"' . $argument . '"';
                        } elseif (is_numeric($argument)) {
                            $arguments .= (string)$argument;
                        } elseif (is_bool($argument)) {
                            $arguments .= ($argument === true ? 'TRUE' : 'FALSE');
                        } elseif (is_array($argument)) {
                            $arguments .= 'array|' . count($argument) . '|';
                        } else {
                            $arguments .= gettype($argument);
                        }
                    }
                }

                $backtraceCode .= sprintf('%03d', (count($trace) - $index)) . ' ' . $class . $step['function'] . '(' . $arguments . ')';

                $backtraceCode .= PHP_EOL;
            }
        }

        return $backtraceCode;
    }



    /**
     * @param array $data
     * @param string $component
     * @return \PunktDe\PtExtbase\Logger\Logger
     */
    public function enrichLogDataByComponent(&$data, $component)
    {
        if (!empty($GLOBALS['TSFE']->fe_user->user['uid'])) {
            $data['UserID'] = $GLOBALS['TSFE']->fe_user->user['uid'];
        }

        $this->enrichLoggerSpecificDataByComponent($data, $component);

        if (empty($component)) {
            $data['loggerComponent'] = $this->loggerManager->unifyComponentName($this->defaultLogComponent);
        } else {
            $data['loggerComponent'] = $this->loggerManager->unifyComponentName($component);
        }

        return $this;
    }

    /**
     * @param array $data
     * @param string $component
     */
    public function enrichLoggerSpecificDataByComponent(&$data, $component)
    {
    }
}
