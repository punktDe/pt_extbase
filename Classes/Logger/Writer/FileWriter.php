<?php
namespace PunktDe\PtExtbase\Logger\Writer;

class FileWriter extends \TYPO3\CMS\Core\Log\Writer\FileWriter
{
    /**
     * Sets the path to the log file.
     *
     * We overwrite this method to allow _absolute_ log paths!
     *
     * @param string $logFile path to the log file
     * @return $this
     */
    public function setLogFile($logFile)
    {
        if (is_resource(self::$logFileHandles)) {
            $this->closeLogFile();
        }

        $this->logFile = $logFile;
        $this->openLogFile();

        return $this;
    }
}
