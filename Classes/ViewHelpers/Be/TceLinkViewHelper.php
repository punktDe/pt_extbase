<?php
namespace PunktDe\PtExtbase\ViewHelpers\Be;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;

class TceLinkViewHelper extends AbstractBackendViewHelper
{

    /**
     * @param string $action
     * @param string $recordType
     * @param integer $uid
     * @param integer $pid
     * @return string
     */
    public function render($action, $recordType, $uid, $pid = 0) {
        $moduleName = 'record_' . $action;

        $urlParameters = [
            $action => [
                $recordType => [
                    (string)$uid => $action
                ]
            ],
            'id' => (string)$pid,
            'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI')
        ];

        return BackendUtility::getModuleUrl($moduleName, $urlParameters);
    }

}
