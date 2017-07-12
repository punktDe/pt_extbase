<?php
namespace PunktDe\PtExtbase\State\Session\Storage\StorageAdapter;

/***************************************************************
 *  Copyright (C) 2017 punkt.de GmbH
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Core\Database\DatabaseConnection;

/**
 * Session Storage Adapter class for TYPO3 Frontend _browser_ sessions and Backend user sessions
 *
 * @author      Rainer Kuhn
 * @author      Michael Knoll
 * @package     State
 * @subpackage  Session
 */
class SessionAdapter implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * Holds singleton instance of this class
     *
     * @var |PunktDe\PtExtbase\State\Session\Storage\StorageAdapter|SessionAdapter
     */
    private static $uniqueInstance = null;


    /**
     * Class constructor: must not be called directly in order to use getInstance() to get the unique instance of the object
     */
    protected function __construct()
    {
    }


    /**
     * @return |PunktDe\PtExtbase\State\Session\Storage\StorageAdapter|SessionAdapter
     */
    public static function getInstance()
    {
        if (self::$uniqueInstance === null) {
            $className = __CLASS__;
            self::$uniqueInstance = new $className();
        }

        return self::$uniqueInstance;
    }


    /**
     * Final method to prevent object cloning (using 'clone'), in order to use only the unique instance of the Singleton object.
     * @param   void
     * @return  void
     */
    final public function __clone()
    {
        throw new \Exception('Clone is not allowed for ' . get_class($this) . ' (Singleton)');
    }


    /**
     * Returns the value of a key from TYPO3 FE _browser_ session or a BE user session (if the session value is serialized it will be returned unserialized)
     *
     * @param   string $key name of session key to get the value of
     * @param   bool $allowUnserializing allow automatic unserializing of objects within this method
     * @return  mixed       associated value from session
     * @throws  \Exception   if no valid frontend user and no valid backend user found
     */
    public static function read($key, $allowUnserializing = true)
    {

        $typoscriptFrontendController = $GLOBALS['TSFE']; /** @var TypoScriptFrontendController $typoscriptFrontendController */
        $beUser = $GLOBALS['BE_USER']; /** @var BackendUserAuthentication $beUser */

        // TYPO3 Frontend mode
        if (TYPO3_MODE == 'FE' && ($typoscriptFrontendController->fe_user instanceof \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication)) {
            $val = $typoscriptFrontendController->fe_user->getKey('ses', $key);
            if (TYPO3_DLOG) {
                GeneralUtility::devLog(sprintf('Reading "%s" from FE browser session in "$GLOBALS[\'TSFE\']->fe_user"', $key), 'pt_extbase');
            }
            if (($allowUnserializing == true) && (is_string($val) && unserialize($val) !== false)) {
                $val = unserialize($val);
            }


            // TYPO3 Backend mode
        } else {
            \Tx_PtExtbase_Assertions_Assert::isInstanceOf($beUser, BackendUserAuthentication::class, ['message' => 'No valid backend user found!']);

            $val = $beUser->getSessionData($key);
            if (TYPO3_DLOG) {
                GeneralUtility::devLog(sprintf('Reading "%s" from BE user session in "$GLOBALS[\'BE_USER\']"', $key), 'pt_extbase');
            }
        }

        return $val;
    }


    /**
     * Saves a value (objects and arrays will be serialized before) into a session key of FE _browser_ session or a BE user session *immediately* (does not wait for complete script execution)
     *
     * @param   string $key name of session key to save value into
     * @param   string $val value to be saved with session key
     * @param   bool $allowSerializing (optional) allow automatic serializing of objects within this method
     * @param   string $foreignSessionId ID of foreign session (other than session currently used for request)
     * @throws  \Exception   if no valid frontend user and no valid backend user found
     */
    public static function store($key, $val, $allowSerializing = true, $foreignSessionId = null)
    {
        $typoscriptFrontendController = $GLOBALS['TSFE']; /** @var TypoScriptFrontendController $typoscriptFrontendController */
        $databaseConnection = $GLOBALS['TYPO3_DB']; /** @var DatabaseConnection $databaseConnection */
        $beUser = $GLOBALS['BE_USER']; /** @var BackendUserAuthentication $beUser */

        // TYPO3 Frontend mode
        if (TYPO3_MODE == 'FE' && ($typoscriptFrontendController->fe_user instanceof \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication)) {
            if (($allowSerializing == true) && (is_object($val) || is_array($val))) {
                $val = serialize($val);
            }

            if (is_null($foreignSessionId)) {
                $typoscriptFrontendController->fe_user->setKey('ses', $key, $val);
                $typoscriptFrontendController->fe_user->sesData_change = 1;
                $typoscriptFrontendController->fe_user->storeSessionData();
                if (TYPO3_DLOG) {
                    GeneralUtility::devLog(sprintf('Storing "%s" into FE browser session using "$GLOBALS[\'TSFE\']->fe_user"', $key), 'pt_extbase');
                }
            } else {
                \Tx_PtExtbase_Assertions_Assert::isString($foreignSessionId);

                // read current foreign session data
                $rows = $databaseConnection->exec_SELECTgetRows(
                    '*',
                    'fe_session_data',
                    'hash=' . $databaseConnection->fullQuoteStr($foreignSessionId, 'fe_session_data')
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
                $databaseConnection->exec_INSERTquery('fe_session_data', $insertFields);
                if (TYPO3_DLOG) {
                    GeneralUtility::devLog(sprintf('Storing "%s" into foreign FE browser session "%s"', $key, $foreignSessionId), 'pt_extbase');
                }
            }

            // TYPO3 Backend mode
        } else {
            \Tx_PtExtbase_Assertions_Assert::isInstanceOf($beUser, BackendUserAuthentication::class, ['message' => 'No valid backend user found!']);

           $beUser->setAndSaveSessionData($key, $val);
            if (TYPO3_DLOG) {
                GeneralUtility::devLog(sprintf('Storing "%s" into BE user session using "$GLOBALS[\'BE_USER\']"', $key), 'pt_extbase');
            }
        }
    }


    /**
     * Deletes a session value from TYPO3 FE _browser_ session or a BE user session
     * *immediately* (does not wait for complete script execution)
     *
     * @param   string $key name of session key to delete (array key)
     * @throws  Exception   if no valid frontend user and no valid backend user found
     */
    public static function delete($key)
    {
        $typoscriptFrontendController = $GLOBALS['TSFE']; /** @var TypoScriptFrontendController $typoscriptFrontendController */
        $databaseConnection = $GLOBALS['TYPO3_DB']; /** @var DatabaseConnection $databaseConnection */
        $beUser = $GLOBALS['BE_USER']; /** @var BackendUserAuthentication $beUser */


        // TYPO3 Frontend mode
        if (TYPO3_MODE == 'FE' && ($typoscriptFrontendController->fe_user instanceof \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication)) {
            if (!empty($typoscriptFrontendController->fe_user->sesData[$key])) {
                unset($typoscriptFrontendController->fe_user->sesData[$key]);
                $typoscriptFrontendController->fe_user->sesData_change = 1;
                $typoscriptFrontendController->fe_user->storeSessionData();
                if (TYPO3_DLOG) {
                    GeneralUtility::devLog(sprintf('Deleting "%s" from FE browser session in "$GLOBALS[\'TSFE\']->fe_user"', $key), 'pt_extbase');
                }
            }

            // TYPO3 Backend mode
        } else {
            \Tx_PtExtbase_Assertions_Assert::isInstanceOf($beUser, BackendUserAuthentication::class, ['message' => 'No valid backend user found!']);

            $sesDat = unserialize($beUser->user['ses_data']);

            if (!empty($sesDat[$key])) {
                unset($sesDat[$key]);
                $beUser->user['ses_data'] = (!empty($sesDat) ? serialize($sesDat) : '');
                // this is adapted from t3lib_userAuth::setAndSaveSessionData()
                $databaseConnection->exec_UPDATEquery($beUser->session_table,
                    'ses_id=' . $databaseConnection->fullQuoteStr($beUser->user['ses_id'], $beUser->session_table),
                    ['ses_data' => $beUser->user['ses_data']]
                );
                if (TYPO3_DLOG) {
                    GeneralUtility::devLog(sprintf('Deleting "%s" from BE user in "$GLOBALS[\'BE_USER\']"', $key), 'pt_extbase');
                }
            }
        }
    }
}
