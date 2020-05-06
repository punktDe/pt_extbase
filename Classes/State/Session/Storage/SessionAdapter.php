<?php
namespace PunktDe\PtExtbase\State\Session\Storage;
use PunktDe\PtExtbase\Assertions\Assert;
use PunktDe\PtExtbase\Logger\Logger;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2005-2011 Rainer Kuhn, Wolfgang Zenker,
 *                Fabrizio Branca, Michael Knoll
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/


/**
 * Session Storage Adapter class for TYPO3 Frontend _browser_ sessions and Backend user sessions
 *
 * @author      Rainer Kuhn
 * @author      Michael Knoll
 * @package     State
 * @subpackage  Session
 */
class SessionAdapter implements AdapterInterface
{

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var Logger
     */
    protected $logger;

    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->logger = $this->objectManager->get(Logger::class);
    }

    /**
     * Returns the value of a key from TYPO3 FE _browser_ session or a BE user session (if the session value is serialized it will be returned unserialized)
     *
     * @param   string $key name of session key to get the value of
     * @param   bool $allowUnserializing allow automatic unserializing of objects within this method
     * @return  mixed       associated value from session
     * @throws  \Exception   if no valid frontend user and no valid backend user found
     */
    public function read($key, $allowUnserializing = true)
    {
        // TYPO3 Frontend mode
        if (TYPO3_MODE === 'FE' && ($GLOBALS['TSFE']->fe_user instanceof FrontendUserAuthentication)) {
            $val = $GLOBALS['TSFE']->fe_user->getKey('ses', $key);
                $this->logger->debug(sprintf('Reading "%s" from FE browser session in "$GLOBALS[\'TSFE\']->fe_user"', $key), __CLASS__);
            if (($allowUnserializing == true) && (is_string($val) && unserialize($val) !== false)) {
                $val = unserialize($val);
            }


            // TYPO3 Backend mode
        } else {
            Assert::isInstanceOf($GLOBALS['BE_USER'], BackendUserAuthentication::class, ['message' => 'No valid backend user found!']);

            $val = $GLOBALS['BE_USER']->getSessionData($key);
            $this->logger->debug(sprintf('Reading "%s" from BE user session in "$GLOBALS[\'BE_USER\']"', $key), __CLASS__);
        }

        return $val;
    }


    /**
     * Saves a value (objects and arrays will be serialized before) into a session key of FE _browser_ session or a BE user session *immediately* (does not wait for complete script execution)
     *
     * @param   string $key name of session key to save value into
     * @param   mixed $val value to be saved with session key
     * @param   bool $allowSerializing (optional) allow automatic serializing of objects within this method
     * @param   string $foreignSessionId ID of foreign session (other than session currently used for request)
     * @throws  \Exception   if no valid frontend user and no valid backend user found
     */
    public function store($key, $val, $allowSerializing = true, $foreignSessionId = null)
    {
        // TYPO3 Frontend mode
        if (TYPO3_MODE === 'FE' && ($GLOBALS['TSFE']->fe_user instanceof FrontendUserAuthentication)) {
            if (($allowSerializing == true) && (is_object($val) || is_array($val))) {
                $val = serialize($val);
            }

            if (is_null($foreignSessionId)) {
                $GLOBALS['TSFE']->fe_user->setKey('ses', $key, $val);
                $GLOBALS['TSFE']->fe_user->sesData_change = 1;
                if ($GLOBALS['TSFE']->fe_user->user === false) {
                    $GLOBALS['TSFE']->fe_user->user = null;
                }
                $GLOBALS['TSFE']->fe_user->storeSessionData();
                $this->logger->debug(sprintf('Storing "%s" into FE browser session using "$GLOBALS[\'TSFE\']->fe_user"', $key), __CLASS__);
            } else {
                Assert::isString($foreignSessionId);

                // read current foreign session data
                $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                    '*',
                    'fe_session_data',
                    'hash=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($foreignSessionId, 'fe_session_data')
                );
                $sessionData = unserialize($rows[0]['content']);

                // update sessionData
                $sessionData[$key] = $val;

                // write sessionData back to database
                $insertFields = [
                    'hash' => $foreignSessionId,
                    'content' => serialize($sessionData),
                    'tstamp' => time()
                ];
                $GLOBALS['TYPO3_DB']->exec_DELETEquery('fe_session_data', 'hash=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($foreignSessionId, 'fe_session_data'));
                $GLOBALS['TYPO3_DB']->exec_INSERTquery('fe_session_data', $insertFields);
                $this->logger->debug(sprintf('Storing "%s" into foreign FE browser session "%s"', $key, $foreignSessionId), __CLASS__);
            }

            // TYPO3 Backend mode
        } else {
            Assert::isInstanceOf($GLOBALS['BE_USER'], BackendUserAuthentication::class, ['message' => 'No valid backend user found!']);

            $GLOBALS['BE_USER']->setAndSaveSessionData($key, $val);
            $this->logger->debug(sprintf('Storing "%s" into BE user session using "$GLOBALS[\'BE_USER\']"', $key), __CLASS__);
        }
    }


    /**
     * Deletes a session value from TYPO3 FE _browser_ session or a BE user session
     * *immediately* (does not wait for complete script execution)
     *
     * @param   string $key name of session key to delete (array key)
     * @throws  \Exception   if no valid frontend user and no valid backend user found
     */
    public function delete($key)
    {

        // TYPO3 Frontend mode
        if (TYPO3_MODE == 'FE' && ($GLOBALS['TSFE']->fe_user instanceof FrontendUserAuthentication)) {
            $GLOBALS['TSFE']->fe_user->setKey('ses', $key, null);
            $GLOBALS['TSFE']->fe_user->sesData_change = 1;
            $GLOBALS['TSFE']->fe_user->storeSessionData();
            $this->logger->debug(sprintf('Deleting "%s" from FE browser session in "$GLOBALS[\'TSFE\']->fe_user"', $key), __CLASS__);

            // TYPO3 Backend mode
        } else {
            Assert::isInstanceOf($GLOBALS['BE_USER'], BackendUserAuthentication::class, ['message' => 'No valid backend user found!']);

            $sesDat = unserialize($GLOBALS['BE_USER']->user['ses_data']);

            if (!empty($sesDat[$key])) {
                unset($sesDat[$key]);
                $GLOBALS['BE_USER']->user['ses_data'] = (!empty($sesDat) ? serialize($sesDat) : '');
                // this is adapted from t3lib_userAuth::setAndSaveSessionData()
//      TODO
//                $GLOBALS['TYPO3_DB']->exec_UPDATEquery($GLOBALS['BE_USER']->session_table,
//                    'ses_id=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($GLOBALS['BE_USER']->user['ses_id'], $GLOBALS['BE_USER']->session_table),
//                    ['ses_data' => $GLOBALS['BE_USER']->user['ses_data']]
//                );
                $this->logger->debug(sprintf('Deleting "%s" from BE user in "$GLOBALS[\'BE_USER\']"', $key), __CLASS__);
            }
        }
    }
}
