<?php
namespace PunktDe\PtExtbase\ViewHelpers\Be;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;

class TceLinkViewHelper extends AbstractBackendViewHelper
{

    /**
     * @var DatabaseConnection
     */
    protected $databaseConnection;

    /**
     * @param string $action
     * @param string $recordType
     * @param integer $uid
     * @param integer $pid
     * @return string
     * @throws \Exception
     */
    public function render($action, $recordType, $uid = 0, $pid = 0) {
        $moduleName = 'record_edit';

        if ($action == 'new') {
            $urlParameters = [
                'edit' => [
                    $recordType => [
                        (string)$pid => $action
                    ]
                ],
                'id' => (string)$pid,
                'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI')
            ];

        } elseif ($action == 'edit') {
            $urlParameters = [
                $action => [
                    $recordType => [
                        (string)$uid => $action
                    ]
                ],
                'id' => (string)$pid,
                'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI')
            ];
        } else {
            throw new \Exception(sprintf('action %s is not yet implemented', $action), 1484586867);
        }

        return BackendUtility::getModuleUrl($moduleName, $urlParameters);
    }

}
