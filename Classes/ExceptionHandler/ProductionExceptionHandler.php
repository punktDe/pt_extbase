<?php
namespace PunktDe\PtExtbase\ExceptionHandler;

/*
 * This file is part of the PunktDe\PtExtbase package.
 *
 * This package is open source software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use TYPO3\CMS\Core\Error\ProductionExceptionHandler as Typo3ProductionExceptionHandler;
use TYPO3\CMS\Core\Messaging\AbstractStandaloneMessage;
use TYPO3\CMS\Core\Messaging\ErrorpageMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ProductionExceptionHandler extends Typo3ProductionExceptionHandler
{

    /**
     * @param \Exception|\Throwable $exception
     */
    public function echoExceptionWeb($exception)
    {
        if (isset($GLOBALS['TYPO3_AJAX']) && $GLOBALS['TYPO3_AJAX'] === true) {
            echo $exception->getCode();
        } else {
            $this->sendStatusHeaders($exception);

            $this->writeLogEntries($exception, self::CONTEXT_WEB);

            $messageObj = GeneralUtility::makeInstance(
                ErrorpageMessage::class,
                $this->getMessage($exception),
                $this->getTitle($exception)
            );
            $messageObj = $this->overrideDisplayMessageForWeb($messageObj);

            $messageObj->output();
        }
    }

    /**
     * This method can be overwritten to control the message the users will be seeing.
     *
     * E.g. you can just set another html file by calling $errorpageMessage->setTemplateFile() or you could
     * create your own object extending the TYPO3 AbstractStandaloneMessage and do your own output
     *
     * If you want to use this or your own ProductionExceptionHandler, make sure to configure them in the install tool/
     * LocalConfiguration by setting
     * $TYPO3_CONF_VARS['SYS']['productionExceptionHandler'] = \PunktDe\PtExtbase\ExceptionHandler\ProductionExceptionHandler::class
     * (or similar)
     *
     * @param ErrorpageMessage $errorpageMessage
     * @return AbstractStandaloneMessage
     */
    protected function overrideDisplayMessageForWeb(ErrorpageMessage $errorpageMessage): AbstractStandaloneMessage
    {
        return $errorpageMessage;
    }

}
