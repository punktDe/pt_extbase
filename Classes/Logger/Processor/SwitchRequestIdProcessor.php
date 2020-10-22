<?php

namespace PunktDe\PtExtbase\Logger\Processor;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

use \TYPO3\CMS\Core\Log\LogRecord;
use \TYPO3\CMS\Core\Log\Processor\AbstractProcessor;

class SwitchRequestIdProcessor extends AbstractProcessor
{
    /**
     * @param LogRecord $logRecord
     * @return LogRecord
     */
    public function processLogRecord(LogRecord $logRecord)
    {
        $requestId = getenv('REQUEST_ID');

        if ($requestId !== false) {
            $logRecord->setRequestId($requestId);
        }

        return $logRecord;
    }
}
