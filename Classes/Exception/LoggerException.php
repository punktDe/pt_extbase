<?php

namespace PunktDe\PtExtbase\Exception;

/*
 *  (c) 2019 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

use TYPO3\CMS\Core\Log\LogLevel;

class LoggerException extends \Exception
{
    /**
     * @var integer
     * @see \TYPO3\CMS\Core\Log\LogLevel
     */
    protected $logLevel;

    /**
     * @var string[]
     */
    protected $data = [];

    /**
     * @param string $message
     * @param int $code
     * @param \Exception|int $logLevel
     * @param \Exception|null $previous
     * @param array $data
     */
    public function __construct($message = '', $code = 0, $logLevel = LogLevel::ERROR, \Exception $previous = null, array $data = [])
    {
        parent::__construct($message, $code, $previous);

        $this->data = $data;
        if (LogLevel::isValidLevel($logLevel)) {
            $this->logLevel = $logLevel;
        } else {
            $this->logLevel = LogLevel::ERROR;
        }
        $this->data = $data;
    }

    /**
     * @return string[]
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return integer
     */
    public function getLogLevel()
    {
        return $this->logLevel;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
