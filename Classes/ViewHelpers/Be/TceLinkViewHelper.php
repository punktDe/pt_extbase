<?php
namespace PunktDe\PtExtbase\ViewHelpers\Be;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;

class TceLinkViewHelper extends AbstractBackendViewHelper
{

    /**
     * Register arguments.
     */
    public function initializeArguments()
    {
        $this->registerArgument('action', 'string', 'Action', true);
        $this->registerArgument('recordType', 'string', 'RecordType', true);
        $this->registerArgument('uid', 'int', 'uid', false, 0);
        $this->registerArgument('pid', 'int', 'pid', false, 0);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function render() {

        $action = $this->arguments['action'];
        $recordType = $this->arguments['recordType'];
        $uid = $this->arguments['uid'];
        $pid = $this->arguments['pid'];

        if ($action === 'new') {
            $urlParameters = [
                'edit' => [
                    $recordType => [
                        (string)$pid => $action
                    ]
                ],
                'id' => (string)$pid,
                'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI')
            ];

        } elseif ($action === 'edit') {
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

        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        return $uriBuilder->buildUriFromRoute('record_edit', $urlParameters);
    }

}
