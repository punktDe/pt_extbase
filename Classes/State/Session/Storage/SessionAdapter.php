<?php
namespace PunktDe\PtExtbase\State\Session\Storage;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

use PunktDe\PtExtbase\Assertions\Assert;
use PunktDe\PtExtbase\Logger\Logger;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class SessionAdapter implements AdapterInterface
{

    /**
     * @var \PunktDe\PtExtbase\Logger\Logger
     */
    protected $logger;

    public function __construct()
    {
        $this->logger = GeneralUtility::makeInstance(Logger::class);
    }

    /**
     * @param string $key
     * @return mixed|null
     * @throws \Exception
     */
    public function read($key)
    {
        $value = '';
        if (TYPO3_MODE === 'FE') {
            $frontendUserAuthentication = $this->getFrontendUserAuthenication();
            if ($frontendUserAuthentication instanceof FrontendUserAuthentication) {
                $value = $frontendUserAuthentication->getKey('ses', $key);
                if (is_string($value) && $this->unserializeValueWithNoAllowedClasses($value) !== false) {
                    $value = $this->unserializeValueWithNoAllowedClasses($value);
                }
            }
        }else {
            Assert::isInstanceOf($GLOBALS['BE_USER'], BackendUserAuthentication::class, ['message' => 'No valid backend user found!']);

            $value = $GLOBALS['BE_USER']->getSessionData($key);
            $this->logger->debug(sprintf('Reading "%s" from BE user session in "$GLOBALS[\'BE_USER\']"', $key), __CLASS__);

        }

        return $value;
    }

    /**
     * @param string $key
     * @param $value
     * @throws \Exception
     */
    public function store($key, $value)
    {
        if (TYPO3_MODE === 'FE') {
            $frontendUserAuthentication = $this->getFrontendUserAuthenication();

            if ($frontendUserAuthentication instanceof FrontendUserAuthentication) {
                if ((is_object($value) || is_array($value))) {
                    $value = serialize($value);
                }
                $frontendUserAuthentication->setKey('ses', $key, $value);
                $frontendUserAuthentication->sesData_change = true;
                $frontendUserAuthentication->storeSessionData();

                $this->logger->debug(sprintf('Storing "%s" into FE browser session using "$GLOBALS[\'TSFE\']->fe_user"',
                    $key), __CLASS__);
            }
        } else {
            Assert::isInstanceOf($GLOBALS['BE_USER'], BackendUserAuthentication::class, ['message' => 'No valid backend user found!']);

            $GLOBALS['BE_USER']->setAndSaveSessionData($key, $value);
            $this->logger->debug(sprintf('Storing "%s" into BE user session using "$GLOBALS[\'BE_USER\']"', $key), __CLASS__);
        }
    }


    /**
     * @param string $key
     * @throws \Exception
     */
    public function delete($key)
    {
        if (TYPO3_MODE === 'FE') {
            $frontendUserAuthentication = $this->getFrontendUserAuthenication();

            if ($frontendUserAuthentication instanceof FrontendUserAuthentication) {
                $frontendUserAuthentication->setKey('ses', $key, null);
                $frontendUserAuthentication->sesData_change = true;
                $frontendUserAuthentication->storeSessionData();
                $this->logger->debug(sprintf('Deleting "%s" from FE browser session in "$GLOBALS[\'TSFE\']->fe_user"',
                    $key), __CLASS__);
            }
        } else {
            Assert::isInstanceOf($GLOBALS['BE_USER'], BackendUserAuthentication::class, ['message' => 'No valid backend user found!']);

            $sesDat = $this->unserializeValueWithNoAllowedClasses($GLOBALS['BE_USER']->user['ses_data']);

            if (!empty($sesDat[$key])) {
                unset($sesDat[$key]);
                $GLOBALS['BE_USER']->user['ses_data'] = (!empty($sesDat) ? serialize($sesDat) : '');

                $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($GLOBALS['BE_USER']->session_table);
                $queryBuilder = $connection->createQueryBuilder(); /** @var QueryBuilder $queryBuilder */

                $queryBuilder
                    ->update($GLOBALS['BE_USER']->session_table)
                    ->where(
                        $queryBuilder->expr()->eq('ses_id', $queryBuilder->createNamedParameter($GLOBALS['BE_USER']->user['ses_id'], \PDO::PARAM_STR))
                    )
                    ->set('ses_data', $GLOBALS['BE_USER']->user['ses_data'])
                    ->execute();

                $this->logger->debug(sprintf('Deleting "%s" from BE user in "$GLOBALS[\'BE_USER\']"', $key), __CLASS__);
            }
        }
    }

    /**
     * @param FrontendUserAuthentication|null $frontendUserAuthentication
     * @return FrontendUserAuthentication|null
     */
    protected function getFrontendUserAuthenication(): ?FrontendUserAuthentication
    {
        $frontendUserAuthentication = null;

        $typoscriptFrontendController = $GLOBALS['TSFE'];
        if ($typoscriptFrontendController instanceof TypoScriptFrontendController &&
            $typoscriptFrontendController->fe_user instanceof FrontendUserAuthentication) {
            $frontendUserAuthentication = $typoscriptFrontendController->fe_user;
        }
        return $frontendUserAuthentication;
    }

    /**
     * @param string $value
     * @return mixed
     */
    protected function unserializeValueWithNoAllowedClasses(string $value)
    {
        return unserialize(
            $value,
            [
                'allowed_classes' => false
            ]
        );
    }
}
