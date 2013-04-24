<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005-2011 Rainer Kuhn, Michael Knoll
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
 * General library class with static helper methods
 * 
 *
 * @author      Rainer Kuhn
 * @package     Div
 */
class Tx_PtExtbase_Div  {

	/**
	 * @var tslib_cObj
	 */
	protected static $cObj;


	/**
     * Class constants: period specifiers for getPeriodAsInt()
    */
    const PERIOD_SECS = 0;      // period as seconds
    const PERIOD_MINS = 1;      // period as minutes
    const PERIOD_HOURS = 2;     // period as hours
    const PERIOD_DAYS = 3;      // period as days
    const PERIOD_WEEKS = 4;     // period as weeks
    const PERIOD_MONTHS = 5;    // period as months
    const PERIOD_YEARS = 6;     // period as months



    /***************************************************************************
     *   SECTION: GENERAL METHODS
     **************************************************************************/

    /**
     * Redirects the user to a local page and optionally stores a value to pass to the redirected page into the TYPO3 session
     *
     * Notice: This method may also be used for reload of the current page after executing a database query (to prevent from double execution of database queries on user page reload).
     *
     * @param   string      local page to redirect to: URL path exclusive domain and leading slash, but including all GET-params (Example: for redirection to http://mydomain.com/contact.html?myGetParam=1 just pass "contact.html?myGetParam=1"). This value may be created using the TYPO3 method pi_getPageLink.
     * @param   mixed       (optional) arbitrary value to pass to the redirected page by storing it into the TYPO3 session (objects and arrays will be serialized, see Tx_PtExtbase_State_Session_Storage_SessionAdapter::store())
     * @param   string      (optional) TYPO3 session key name to store $keepVal into (default = 'redirectionKeepVal'). This string may be prefixed with the prefixId of the calling FE plugin to prevent namespace conflicts between different extensions.
     * @return  void
     * @see     tslib_pibase::pi_getPageLink()
     * @author  Rainer Kuhn 
     */
    public static function localRedirect($localPath, $keepVal=NULL, $keepValSessionKeyName='redirectionKeepVal') {

        // hook: allow things to be done before redirecting
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['pt_extbase']['tx_ptextbase_div']['localRedirect'])) {
            $fakeThis = new stdClass();
            $params = array(
                'localPath' => $localPath,
                'keepVal' => $keepVal,
                'keepValSessionKeyName' => $keepValSessionKeyName,
            );
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['pt_extbase']['tx_ptextbase_div']['localRedirect'] as $funcName) {
                t3lib_div::callUserFunction($funcName, $params, $fakeThis);
            }
        }

        // register page overlapping values in TYPO3 session if message has been set
        if (!empty($keepVal)) {
            Tx_PtExtbase_StorageAdapter_StorageAdapter::getInstance()->store($keepValSessionKeyName, $keepVal);
        }

        // generate absolute URL from local path
        $targetUrl  = t3lib_div::locationHeaderUrl($localPath);

        // log to devlog
        if (TYPO3_DLOG) t3lib_div::devLog('Redirecting from "'.t3lib_div::getIndpEnv('TYPO3_REQUEST_URL').'" to "'.$targetUrl.'"', 'pt_extbase', 1);

        // redirect by sending a "Location" header
        header('Location: '.$targetUrl);
        exit;

    }

    
    
    /**
     * Returns an object reference to the hook object if any, false otherwise
     *
     * @param   string          TYPO3 extension key of the extension to use the hook in (e.g. 'pt_gsashop')
     * @param   string          name of hook array of the extensions $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extKey] in ext_localconf.php (e.g. 'pi3_hooks')
     * @param   string          name of the function you want to call / hook key
     * @global  array           $TYPO3_CONF_VARS
     * @return  object|bool     hook object or false if no hook was registered
     * @throws  Tx_PtExtbase_Exception_Exception    if hook method registered, but not found
     * @author  Rainer Kuhn , based on tx_indexedsearch::hookRequest() by Kasper Sk�rh�j/Christian Jul Jensen
     */
    public static function hookRequest($extKey, $hookArrayKey, $functionName) {

        // check if there are any hook relevant userfunctions implemented
        if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extKey][$hookArrayKey][$functionName]) {

            $hookObj = t3lib_div::getUserObj($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extKey][$hookArrayKey][$functionName], '');

            if (method_exists($hookObj, $functionName)) {
                if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Hook method found [%s][%s][%s], returning hook object (class: "%s")', $extKey, $hookArrayKey, $functionName, get_class($hookObj)), 'pt_extbase', 1);

                $hookObj->pObj = self;
                return $hookObj;

            } else {
                throw new Tx_PtExtbase_Exception_Exception('Hook method not found!', 3,
                                               'HOOK ERROR: method '.$functionName.' not found or no hook object returned');
            }
        }

        return false;

    }

    
    
    /**
     * Returns the charset encoding currently used by the TYPO3 website
     *
     * @param   string      (optional) default charset if there is no other setting found (default='iso-8859-1')
     * @global  object      $GLOBALS['TSFE']
     * @global  object      $GLOBALS['LANG']
     * @global  array       $GLOBALS['TYPO3_CONF_VARS']
     * @return  string      charset encoding currently used by TYPO3 (lowercase string)
     * @author  Rainer Kuhn , based on a proposal by Martin Kutschker on http://lists.netfielders.de/pipermail/typo3-team-core/2006-May/004250.html
     * @see     http://typo3.org/documentation/document-library/references/doc_core_tsref/current/view/7/3/
     */
    public static function getSiteCharsetEncoding($defaultCharset='iso-8859-1') {

        $charset = '';

        // do charset detection for FE and BE  ### TODO: do investigation and/or tests to find out if this is correct/sufficient...
        if (is_object($GLOBALS['TSFE']) && $GLOBALS['TSFE']->renderCharset) {
            $charset = $GLOBALS['TSFE']->renderCharset;
        } elseif (is_object($GLOBALS['LANG']) && $GLOBALS['LANG']->charSet) {
            $charset = $GLOBALS['LANG']->charSet;
        } elseif ($GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset']) {
            $charset = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'];
        } else {
            $charset = $defaultCharset;
        }

        return strtolower($charset);

    }

    
    
    /**
     * Checks if a file exists in the includepath and includes it once if it is available
     *
     * @param   string      name of the file to include
     * @return  boolean     FALSE if file was not available/included, TRUE otherwise
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
	  * @deprecated
     */
    public static function includeOnceIfExists($filename) {

        if (self::fileExistsInIncpath($filename)) {
            include_once $filename;
            return true;
        } else {
            return false;
        }

    }
    
    

    /**
     * Checks if a file exists in the include path and returns the full path if the file exists
     *
     * @param       string      name of the file to look for
     * @return      mixed       (string) the full path if file exists, (boolean) FALSE if it does not
     * @author      Aidan Lister <aidan@php.net>, added to pt_extbase by Fabrizio Branca <mail@fabrizio-branca.de>
     * @version     1.2.1
     * @link        http://aidanlister.com/repos/v/function.file_exists_incpath.php
     */
    public static function fileExistsInIncpath($filename) {

        $paths = explode(PATH_SEPARATOR, get_include_path());

        foreach ($paths as $path) {
            // formulate the absolute path
            $fullpath = $path . DIRECTORY_SEPARATOR . $filename;
            // check it
            if (file_exists($fullpath)) {
                return $fullpath;
            }
        }

        return false;

    }
    
    

    /**
     * Creates an easy-to-remember mnemonic password.
     * All passwords created by this method follow the scheme "every consonant is followed by a vowel" (e.g. "rexegubo")
     *
     * @param   integer     required length of password (optional, default is 8)
     * @return  string      mnemonic password
     * @author  Rainer Kuhn 
     */
    public static function createPassword($length=8) {

        $consonantArr  = array('b','c','d','f','g','h','j','k','l','m','n','p','r','s','t','v','w','x','y','z');
        $vowelArr  = array('a','e','i','o','u');
        $password = '';

        if (!is_int($n = $length/2)) {
            $n = (integer)$length/2;
            $password .= $vowelArr[rand(0, 4)];
        }

        for ($i=1; $i<=$n; $i++) {
            $password .= $consonantArr[rand(0, 19)];
            $password .= $vowelArr[rand(0, 4)];
        }

        return $password;

    }
    
    

    /**
     * Get Pid from parameter (pid or alias)
     *
     * @param   string  pid or alias
     * @param   bool    (optional) allow "0" as pid, default: false
     * @return  int     pid
     * @throws  Tx_PtExtbase_Exception_Exception     if pid or alias does not exist, if query fails or if pid == 0 and allowPidZero is false
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     */
    public static function getPid($pidOrAlias, $allowZeroAsPid=false) {
        
        static $cache = array();
        $cacheKey = $pidOrAlias . _ . ($allowZeroAsPid ? '1' : '0');
        
        if (!isset($cache[$cacheKey])) {

            $select  = 'uid';
            $from    = 'pages';
            if (!ctype_digit(strval($pidOrAlias))) {
                // cannot be a pid, check if there is a page with this alias and get its pid
                $where = 'alias = '.$GLOBALS['TYPO3_DB']->fullQuoteStr(trim(strval($pidOrAlias)), $from);
            } else {
                // might be a pid, check if the page exists
                if (!$allowZeroAsPid && intval($pidOrAlias) == 0) {
                    throw new Tx_PtExtbase_Exception_Exception('PID "0" is not allowed here');
                }
                $where = 'uid = '.intval($pidOrAlias);
            }
            $where .= self::enableFields($from);
            $groupBy = '';
            $orderBy = '';
            $limit   = '1';
    
            // exec query using TYPO3 DB API
            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);
            if ($res == false) {
                throw new Tx_PtExtbase_Exception_Exception('Query failed', 1, $GLOBALS['TYPO3_DB']->sql_error());
            }
            $a_row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
            $GLOBALS['TYPO3_DB']->sql_free_result($res);
    
            if ($a_row == false) {
                throw new Tx_PtExtbase_Exception_Exception('PID "'.$pidOrAlias.'" not found');
            }
    
            $cache[$cacheKey] = $a_row['uid'];
        }
        
        return $cache[$cacheKey];
    }
    
    

    /**
     * Output HTML to a popup window
     *
     * @param   string  html code
     * @param   string  (optional) name of the var holding the reference to the window, default is '_popup'
     * @param   string  (optional) window parameter string, default is 'width=1280,height=600,resizable,scrollbars=yes'
     * @param   string  (optional) url of the new window, keep empty to display your html code
     * @param   string  (optional) name of the window
     * @return  bool    true if popup was rendered, otherwise false
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     */
    public static function outputToPopup($htmlCode, $varName = '_popup', $windowParams = 'width=1280,height=600,resizable,scrollbars=yes', $windowUrl = '', $windowName = '') {

        if (is_object($GLOBALS['TSFE'])) {

            $jscode = $varName.' = window.open("'.$windowUrl.'","'.$windowName.'","'.$windowParams.'");'.chr(10);
            foreach(explode(chr(10), $htmlCode) as $line) {
                $line = strtr($line, array('"' => '\\"'));
                $jscode .= $varName .'.document.writeln("'.$line.'");'.chr(10);
            }
            $jscode .= $varName .'.document.close();'.chr(10);

            $GLOBALS['TSFE']->additionalHeaderData['popup'.$varName] .= t3lib_div::wrapJS($jscode);
            return true;
        } else {
            return false;
        }

    }
    
    

    /**
     * Clear caches "pages", "all", "temp_CACHED" or numeric'
     *
     * @param   mixed
     * @return  void
     * @throws  Tx_PtExtbase_Exception_Exception    if parameter is not valid
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     * @see     t3lib_TCEmain::clear_cacheCmd
     */
    public static function clearCache($cacheCmd = 'all') {

        if (!t3lib_div::testInt($cacheCmd) && !in_array($cacheCmd, array('pages', 'all', 'temp_CACHED'))) {
            throw Tx_PtExtbase_Exception_Exception('Parameter must be "pages", "all", "temp_CACHED" or numeric');
        }

        $tce = t3lib_div::makeInstance('t3lib_TCEmain'); /* @var $tce t3lib_TCEmain */
        $tce->stripslashes_values = 0;
        $tce->start(Array(),Array());
        $tce->clear_cacheCmd($cacheCmd);

    }

    
    
    /**
     * Redirects to the Cookie Error Page if Cookie is not set and Cookie Error Page is defined. Otherwise nothing is done
     *
     * @param   object   tslib_pibase
     * @return  boolean  true if Cookies enabled, false if Cookies not disabled and no Error Page set or does not exist.
     * @author  Dorit Rottner <rottner@punkt.de>
     */
     public static function checkCookies(tslib_pibase $pObj) {

        if (!$_COOKIE['fe_typo_user']) {
            $redirect_url = $pObj->pi_linkTP_keepPIvars_url($overrulePIvars = array(), $cache = 1, $clearAnyway = 0, $GLOBALS['TSFE']->tmpl->setup['config.']['pt_extbase.']['cookieErrorPage']);
            if ($redirect_url) {
                t3lib_div::devLog('Cookie not set redirect to '.$redirect_url, 'pt_extbase', 1);
                header('Location: '.t3lib_div::locationHeaderUrl($redirect_url));
                exit;
            } else {
                t3lib_div::devLog('Cookie not set redirect url not specified. ', 'pt_extbase', 1);
                $return = false;
            }
        } else {
            $return = true;
        }

        return $return;

    }
    
    

    /**
     * Check if a user has access to an item
     * (get the group list of the current logged in user from $GLOBALS['TSFE']->gr_list)
     *
     * @param   string      comma-separated list of fe_group uids from a user
     * @param   string      comma-separated list of fe_group uids of the item to access
     * @return  bool        true if at least one of the users group uids is in the access list or the access list is empty
     * @see     t3lib_pageSelect::getMultipleGroupsWhereClause()
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     */
    public static function hasGroupAccess($groupList, $accessList) {

        if (empty($accessList)) {
            return true;
        }
        foreach(t3lib_div::intExplode(',', $groupList) as $groupUid) {
            if (t3lib_div::inList($accessList, $groupUid)) {
                return true;
            }
        }
        return false;

    }
    
    

    /**
     * Quote string method for usage as a Typoscript userFunction to prevent SQL injections when using data from the clients in SQL statements (RECORDS, CONTENT)
     * You can pass the table name as a config option (see example). The table name is a string/stdWrap field
     *
     * @example
     * <code>
     * page.includeLibs.tx_ptextbase_div = EXT:pt_extbase/Classes/Div.php
     *
     * lib.searchByName = CONTENT
     * lib.searchByName {
     *      table = pages
     *      select {
     *          where = 1=1
     *          andWhere.stdWrap {
     *              cObject = TEXT
     *              cObject {
     *                  data = GPvar:tx_myext_searchword
     *                  postUserFunc = Tx_PtExtbase_Div->quoteStr
     *                  postUserFunc.table = pages
     *              }
     *              wrap = title LIKE "%|%"
     *          }
     *      }
     * }
     * </code>
     *
     * @param   string      content
     * @param   array       (optional) configuration, do not use a type hint here
     * @return  string      quoted string
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     */
    public static function quoteStr($content, $conf) {

        $conf['table'] = $GLOBALS['TSFE']->cObj->stdWrap($conf['table'], $conf['table.']);

        $quotedString = $GLOBALS['TYPO3_DB']->quoteStr($content, $conf['table']);

        return $quotedString;

    }
    
    

    /**
     * Checks whether a given array is an associative array
     *
     * @param   array       array to be checked
     * @return  boolean     true, if array is associative
     * @see     http://de.php.net/is_array
     * @author  Michael Knoll <knoll@punkt.de>
     */
    public static function isAssociativeArray($array) {

        if (is_array($array)) {
            foreach (array_keys($array) as $k => $v) {
                if ($k !== $v) {
                    return true;
                }
            }
        }

        return false;

    }
    
    

    /**
     * Checks if a value is "integerish"
     *
     * @param   mixed   value to check
     * @return  bool    true if integerish
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     */
    public static function isIntegerish($val) {
        // or: is_int($val) || ctype_digit($val)
        return ('x'.$val == 'x'.intval($val));
    }



    /**
     * Applies a stdWrap on all items of an array
     *
     * @param   array   input array
     * @return  array   output array
     * @author  Fabrizio Branca <mail@fabrizio-branca.de> (taken from ext:tcaobjects)
     */
    public static function stdWrapArray(array $data, tslib_cObj $cObj=NULL) {

        if (is_null($cObj)) {
            $cObj = $GLOBALS['TSFE']->cObj;
        }

        Tx_PtExtbase_Assertions_Assert::isInstanceOf($cObj, 'tslib_cObj', array('message' => 'No cObj found.'));

        $newData = array();
        foreach (array_keys($data) as $key) {
            if (substr($key, -1) != '.') {
                if (empty($newData[$key])) {
                    $newData[$key] = $cObj->stdWrap($data[$key], $data[$key.'.']);
                }
            } else {
                if (empty($newData[substr($key, 0, -1)])) {
                    $newData[substr($key, 0, -1)] = $cObj->stdWrap($data[substr($key, 0, -1)], $data[$key]);
                }
            }
        }
        return $newData;
    }




    /***************************************************************************
        SECTION: DATE/TIME METHODS
    ***************************************************************************/

    /**
     * Converts a given date string from Euro format to US format or vice versa and returns the converted string
     *
     * @param   string      date string to convert
     * @param   boolean     (optional) flag for conversion direction: 0 = converts DD.MM.YYYY/DD-MM-YYYY to YYYY-MM-DD (default), 1 = converts YYYY-MM-DD to DD.MM.YYYY
     * @return  string      converted date (YYYY-MM-DD by default, if 2. param is set to true DD.MM.YYYY)
     * @author  Rainer Kuhn 
     */
    public static function convertDate($dateOrig, $reverse=0) {

        Tx_PtExtbase_Assertions_Assert::isNotEmptyString($dateOrig);

        $dateConv = strtr($dateOrig, '.', '-');
        $dateElements = explode('-', $dateConv);
        $seperator = ($reverse == 1 ? '.' : '-');
        $dateConv = implode($seperator, array_reverse($dateElements));

        return $dateConv;

    }
    
    

    /**
     * Returns the current date
     *
     * @param   boolean     (optional) flag for date format: 0 = YYYY-MM-DD (default), 1 = DD.MM.YYYY
     * @return  string      current date (YYYY-MM-DD by default, if 2. param is set to true DD.MM.YYYY)
     * @author  Rainer Kuhn 
     */
    public static function dateToday($euroFormat=0) {

        $today = ($euroFormat == 1 ? date('d.m.Y') : date('Y-m-d'));

        return $today;

    }

    
    
    /**
     * Converts string describing time period into integer of given unit
     *
     * @param   string  string describing period as understood by strtotime()
     * @param   integer requested unit
     * @param   boolean (optional) round to nearest value in unit calculation instead of cutting (default: true)
     * @return  integer numeric difference between now and given period in given unit
     * @author  Wolfgang Zenker <zenker@punkt.de>
    */
    public function getPeriodAsInt($period, $unit, $round = true) {
        $result = 0;

        if ($period != '') {
            $tz = timezone_open('Europe/Berlin');
            $newdate = date_create($period, $tz);
            $newtime = $newdate->format('U');
            $now = time();
            $timediff = (double) ($newtime - $now);
            switch ($unit) {
                case self::PERIOD_SECS:
                    $result = intval($timediff);
                    break;
                case self::PERIOD_MINS:
                    $result = $timediff / 60.0;
                    $result = $round ? round($result) : intval($result);
                    break;
                case self::PERIOD_HOURS:
                    $result = $timediff / 3600.0;
                    $result = $round ? round($result) : intval($result);
                    break;
                case self::PERIOD_DAYS:
                    $result = $timediff / 86400.0;
                    $result = $round ? round($result) : intval($result);
                    break;
                case self::PERIOD_WEEKS:
                    $result = $timediff / 604800.0;
                    $result = $round ? round($result) : intval($result);
                    break;
                case self::PERIOD_MONTHS:
                    // we use the average month length of 1/12th of a year
                    $result = $timediff / 2629800.0;
                    $result = $round ? round($result) : intval($result);
                    break;
                case self::PERIOD_YEARS:
                    // we use 365,25 days for one year
                    $result = $timediff / 31557600.0;
                    $result = $round ? round($result) : intval($result);
                    break;
                default:
                    throw new Tx_PtExtbase_Exception_Internal('unknown unit');
            }
        }

        return $result;
    }



    /***************************************************************************
     *   SECTION: LANGUAGE SPECIFIC METHODS
     **************************************************************************/

    /**
     * Returns a frontend locallang value with all HTML entities replaced for display in browser
     *
     * @param   tslib_pibase      object of type tslib_pibase: TYPO3 frontend plugin derived from tslib_pibase
     * @param   string      locallang array key (of the plugins locallang.php file)
     * @return  string      value of the passed locallang array key with all HTML entities replaced for display in browser
     * @author  Rainer Kuhn 
     */
    public static function displayLL(tslib_pibase $callerObj, $LLkey) {

        return htmlentities($callerObj->pi_getLL($LLkey), ENT_QUOTES);

    }
    
    

    /**
     * Includes a locallang file and returns the $LOCAL_LANG array found inside - works for frontend and backend.
     * This method provides a TYPO3_MODE independent version of the seperate TYPO3 FE/BE methods getLLL().
     *
     * @param   string      reference to a relative filename to include (if exists): that file is expected to be a 'local_lang' file containing a $LOCAL_LANG array
     * @return  array       $LOCAL_LANG array found in the included file if that array is found, otherwise an empty array
     * @see     tslib_fe::readLLfile() = TYPO3 FE method
     * @see     language::readLLfile() = TYPO3 BE method
     * @author  Rainer Kuhn 
     */
    public static function readLLfile($llFile) {

            $llArray = array();

            // TYPO3 Frontend mode
            if (TYPO3_MODE == 'FE' && is_object($GLOBALS['TSFE'])) {
                $llArray = $GLOBALS['TSFE']->readLLfile($llFile);
            // TYPO3 Backend mode
            } elseif (is_object($GLOBALS['LANG'])) {
                // $llArray = $GLOBALS['LANG']->readLLfile($llFile);
                // as the function readLLfile is protected in the latest TYPO3 version we read the ll file directly
                $llArray = t3lib_div::readLLfile($llFile, $GLOBALS['LANG']->lang, $GLOBALS['LANG']->charSet); 
            } else {
                throw new Tx_PtExtbase_Exception_Exception('No valid TSFE or LANG object found!');
            }

            return $llArray;

    }
    
    

    /**
     * Returns the locallang label for a specified key from a given $LOCAL_LANG array - works for frontend and backend.
     * This method provides a TYPO3_MODE independent version of the seperate TYPO3 FE/BE methods getLLL().
     *
     *
     * @param   string      locallang key to retrieve it's label
     * @param   array       $LOCAL_LANG array to use - this could be retrieved e.g. by Tx_PtExtbase_Div::readLLfile()
     * @return  string      locallang label for the specified key
     * @see     tslib_fe::getLLL() = TYPO3 FE method
     * @see     language::getLLL() = TYPO3 BE method
     * @author  Rainer Kuhn 
     */
    public static function getLLL($llKey, $llArray) {

        $llLabel = '';

        // TYPO3 Frontend mode
        if (TYPO3_MODE == 'FE' && is_object($GLOBALS['TSFE'])) {
            $llLabel = $GLOBALS['TSFE']->getLLL($llKey, $llArray);
            $llLabel = $GLOBALS['TSFE']->csConv($llLabel); // convert to correct characterset (this not done in $GLOBALS['TSFE']->getLLL)
        // TYPO3 Backend mode
        } else {
            $llLabel = $GLOBALS['LANG']->getLLL($llKey, $llArray);
        }

        return $llLabel;

    }
    
    

    /**
     * Get language object
     *
     * @param   void
     * @return  language    Language object
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     */
    public static function getLangObject() {

        if ($GLOBALS['LANG'] instanceof language) {
            $lang = $GLOBALS['LANG'];
        } else {
            $lang = t3lib_div::makeInstance('language');
            $lang->csConvObj = t3lib_div::makeInstance('t3lib_cs');
        }
        return $lang;

    }



	/**
	 * return the cObj object
	 *
	 * @return tslib_cObj;
	 */
	public static function getCobj() {
		if(!self::$cObj instanceof tslib_cObj) {
			if(TYPO3_MODE == 'FE') {
				if(!is_a($GLOBALS['TSFE']->cObj,'tslib_cObj')) {
					$GLOBALS['TSFE']->newCObj();
				}
			} else {
				t3lib_div::makeInstance('Tx_PtExtbase_Utility_FakeFrontendFactory')->createFakeFrontend();
			}
			self::$cObj = $GLOBALS['TSFE']->cObj;
		}

		return self::$cObj;
	}



    /***************************************************************************
     *   SECTION: EXTENSION CONFIGURATION RETRIEVAL METHODS
     **************************************************************************/

    /**
     * Returns the basic extension configuration data from localconf.php (configurable in Extension Manager)
     *
     * @param   string      extension key of the extension to get its configuration
     * @param   bool        (optional) if true the method won't throw an exception if no configuration is found, default: false
     * @global  array       $TYPO3_CONF_VARS
     * @return  array       basic extension configuration data from localconf.php
     * @throws  Tx_PtExtbase_Exception_Exception   if no basic extension configuration is found in localconf.php or if extKey is empty
     * @author  Rainer Kuhn 
     */
    public static function returnExtConfArray($extKey, $noExceptionIfNoConfigFound=false) {
		if (file_exists(PATH_typo3conf.'localconf.php')) {
			require(PATH_typo3conf.'localconf.php');  // don't use require_once here!

			Tx_PtExtbase_Assertions_Assert::isNotEmptyString($extKey);

			$baseConfArr = unserialize($TYPO3_CONF_VARS['EXT']['extConf'][$extKey]);
		} else {
			$baseConfArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extKey]);
		}
		if (!is_array($baseConfArr)) {
			if ($noExceptionIfNoConfigFound == true) {
				$baseConfArr = array();
			} else {
				throw new Tx_PtExtbase_Exception_Exception('Extension configuration in localconf.php for extension "'.$extKey.'" not found!', 2,
					'$TYPO3_CONF_VARS["EXT"]["extConf"]["'.$extKey.'"] not found in localconf.php.');
			}
		}
		return $baseConfArr;
    }
    
    

    /**
     * Returns typoscript configuration independent of frontend or backend context and caches it into the registry (with the key: ts_$tsConfigKey) to prevent multiple configuration loading.
     *
     * When calling this method with 'plugin.my_ext.' the whole typoscript configuration under this path is stored into the registry.
     * But: When calling this method with 'plugin.my_ext.anotherkey.' the configuration is loaded again instead of looking for the key 'anotherkey.' in the previously loaded configuration.
     * To improve performance always call the highest level and pick the keys after: $conf = Tx_PtExtbase_Div::typoscriptRegistry('plugin.my_ext.'); $key = $conf['anotherkey.']
     *
     * @example
     *  If being only in frontend context:
     *  - Tx_PtExtbase_Div::typoscriptRegistry('plugin.my_ext.');
     *
     *  For all cases, when already having the page uid from where to load the typscript available (will only be used in non-frontend-context)
     *  - Tx_PtExtbase_Div::typoscriptRegistry('plugin.my_ext.', 1);
     *
     *  For all cases, assuming the pageUid is configured in "tsConfigurationPid" of the extension manager configuration (will only be used in non-frontend-context)
     *  - Tx_PtExtbase_Div::typoscriptRegistry('plugin.my_ext.', NULL, 'my_ext', 'tsConfigurationPid');
     *
     * @param     string    typoscript config key, e.g. "plugin.tx_myext."
     * @param     int       (optional) pageuid
     * @param     string    (optional) extension key
     * @param     string    (optional) extConfKey, e.g. "tsConfigurationPid"
     * @return    array     typoscript configuation array
     * @author    Fabrizio Branca <mail@fabrizio-branca.de>
     */
    public static function typoscriptRegistry($tsConfigKey, $pageUid = NULL, $extKey = '', $extConfKey = '') {

        Tx_PtExtbase_Assertions_Assert::isNotEmptyString($tsConfigKey, array('message' => 'No "tsConfigKey" defined!'));

        require_once t3lib_extMgm::extPath('pt_extbase').'Classes/Registry/Registry.php';

        $registry = Tx_PtExtbase_Registry_Registry::getInstance();
        $registryKey = 'ts_' . $tsConfigKey;

        if (!$registry->has($registryKey)) {

            // In frontend context
            if ($GLOBALS['TSFE'] instanceof tslib_fe) {

                $confArray = self::getTS($tsConfigKey);

            // Not in frontend context
            } else {
                if (!is_null($pageUid)) {

                    Tx_PtExtbase_Assertions_Assert::isValidUid($pageUid, false, array('message' => 'No valid pageUid given'));
                    $confArray = self::returnTyposcriptSetup($pageUid, $tsConfigKey);

                } elseif (!empty($extKey) && !empty($extConfKey)) {

                    $tmpExtConfArray = self::returnExtConfArray($extKey);
                    $pageUid = $tmpExtConfArray[$extConfKey];
                    Tx_PtExtbase_Assertions_Assert::isValidUid($pageUid, false, array('message' => 'No valid pageUid found under "'.$extConfKey.'" in extension configuration for extKey "'.$extKey.'"'));
                    $confArray = self::returnTyposcriptSetup($pageUid, $tsConfigKey);

                } else {
                    throw new Tx_PtExtbase_Exception_Exception('You have to define either a "pageUid" or a "extKey" and a "extConfKey" when not in frontend context.');
                }
            }

            $registry->register($registryKey, $confArray);

        }

        return $registry->get($registryKey);

    }
    
    

    /**
     * Returns the Typoscript setup of a given page - this may be used e.g. to read TS frontend configurations where no TSFE exists (e.g. in backend modules or CLI scripts).
     *
     * @param   integer     (optional) page UID of the page to extract its TS setup (default=1)
     * @param   string      (optional) TS config key string to retrieve its value ('.' at each subkey's end means retrieve array, no '.' means retrieve single value, e.g. 'config.tx_ptgsashop.' for array or 'config.tx_ptgsashop.currencyCode' for single value
     * @return  mixed       array or single value - depending on 2nd param $tsConfigKey. If 2nd param is not set: multidimensional array of the given page's TS setup (this may be used read e.g. $returnArr['plugin.']['tx_EXTENSION_pi1.'])
     * @throws  Tx_PtExtbase_Exception_Exception   if no TS setup could be found/created
     * @author  Rainer Kuhn , based on an idea of Fabian Koenig (http://lists.netfielders.de/pipermail/typo3-german/2007-May/032473.html)
     */
    public static function returnTyposcriptSetup($pageUid=1, $tsConfigKey='') {

        // include required TYPO3 libraries
        require_once(PATH_t3lib.'class.t3lib_page.php');
        require_once(PATH_t3lib.'class.t3lib_tsparser_ext.php');
        require_once(PATH_t3lib.'class.t3lib_befunc.php');

        // This method expects that there is not TSFE. If there is (or parts of it - like in the preBeUser hook) the following lines might fail.
        // So we unset TSFE after copying it to a temp variable if it exists and restore it afterwards
        if (is_object($GLOBALS['TSFE'])) {
            $tmpTSFE = $GLOBALS['TSFE'];
            unset($GLOBALS['TSFE']);
        }

        // create TS configuration: idea of Fabian Koenig (http://lists.netfielders.de/pipermail/typo3-german/2007-May/032473.html)
        $sysPageObj = t3lib_div::makeInstance('t3lib_pageSelect');
        $rootLine = $sysPageObj->getRootLine($pageUid);
        $TSObj = t3lib_div::makeInstance('t3lib_tsparser_ext');  /* @var $TSObj t3lib_tsparser_ext */
        $TSObj->tt_track = 0;
        $TSObj->init();
        $TSObj->runThroughTemplates($rootLine);
        $TSObj->generateConfig();

        // restoring the TSFE if there was any
        if (is_object($tmpTSFE)) {
            $GLOBALS['TSFE'] = $tmpTSFE;
        }

        // retrieve complete TS setup
        $returnVal = $TSObj->setup;  // multidimensional TS array here - this may be used read e.g. $returnVal['plugin.']['tx_EXTENSION_pi1.']
        if (!is_array($returnVal)) {
            throw new Tx_PtExtbase_Exception_Exception('TS configuration retrieval error!', 2,
                                           __METHOD__.' failed to retrieve the TS configuration of page'.$pageUid);
        }

        // process return value depending on 2nd param $tsConfigKey
        if (!empty($tsConfigKey)) {
            $returnVal = self::getTS($tsConfigKey, $TSObj->setup);
        }

        return $returnVal;  // mixed (array or single value)

    }
    
    

    /**
     * Get Typoscript from array
     *
     * @example To get an array: Tx_PtExtbase_Div::getTS('plugin.my_ext.');
     * @example To get a single value: Tx_PtExtbase_Div::getTS('plugin.my_ext.my_key');
     * @param   string      typoscript path
     * @param   array       (optional) typoscript array, if empty using $GLOBALS['TSFE']->tmpl->setup
     * @return  mixed       typoscript array or single value
     * @throws    Tx_PtExtbase_Exception_Assertion     if no tsArray is given and not being in a frontend context
     * @throws    Tx_PtExtbase_Exception_Assertion     if tsPath is not valid
     * @throws    Tx_PtExtbase_Exception_Exception            if subKey was not found
     * @author  Rainer Kuhn , Fabrizio Branca <mail@fabrizio-branca.de>
     */
    public static function getTS($tsPath, array $tsArray = array()) {

        Tx_PtExtbase_Assertions_Assert::isNotEmptyString($tsPath, array('message' => '"tsPath" is empty!'));
        // TODO: improve pattern, so that ".blub" or "plugin..test" are not matched

        if (empty($tsArray)) {
            Tx_PtExtbase_Assertions_Assert::isInstanceOf($GLOBALS['TSFE'], 'tslib_fe', array('message' => 'No TSFE available!'));
            $tsArray = $GLOBALS['TSFE']->tmpl->setup;
        }

        Tx_PtExtbase_Assertions_Assert::isNotEmpty($tsArray);

        $lastKeyIsArray = false;
        if (substr($tsPath, -1) == '.') {
            $lastKeyIsArray = true;
        }
        $keyPartsArray = explode('.', $tsPath);
        for ($i=0; $i<count($keyPartsArray); $i++) {
            if (!empty($keyPartsArray[$i])) {
                $newSubKey = $keyPartsArray[$i].(($i<(count($keyPartsArray)-1) || $lastKeyIsArray == true) ? '.' : '');
                $tsArray = $tsArray[$newSubKey];
            }
        }

        return $tsArray;
    }

    
    
    /**
     * Overwrites the conf array with parameters from the flexform with the same keys
     *
     * @param   mixed    plugin object, e.g. "tslib_pibase" or any other object (needs a "tslib_cObj" at ->cObj, an array at ->conf and callable methods "pi_getFFvalue()" and "pi_initPIflexForm()")
     * @param   bool     (optional) if true the method won't throw an exception if no flexform data is found, default: false
     * @return  void
     * @throws  Tx_PtExtbase_Exception_Exception    if no flexform data was found
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     */
    public static function mergeConfAndFlexform($pObj, $noExceptionIfNoFlexform = false) {

        Tx_PtExtbase_Assertions_Assert::isObject($pObj, array('message' => '"$pObj" is no object.'));
        Tx_PtExtbase_Assertions_Assert::isInstanceOf($pObj->cObj, 'tslib_cObj', array('message' => '"$pObj->cObj" is no instance of "tslib_cObj".'));
        Tx_PtExtbase_Assertions_Assert::isArray($pObj->conf, array('message' => '"$pObj->conf" is no array.'));
        if (!is_callable(array($pObj, 'pi_initPIflexForm'))) {
            throw new Tx_PtExtbase_Exception_Exception('"$pObj needs a callable method "pi_initPIflexForm()"');
        }
        if (!is_callable(array($pObj, 'pi_getFFvalue'))) {
            throw new Tx_PtExtbase_Exception_Exception('"$pObj needs a callable method "pi_getFFvalue()"');
        }

        $pObj->pi_initPIflexForm();
        $piFlexForm = $pObj->cObj->data['pi_flexform'];

        if (is_array($piFlexForm['data'])) {
            foreach ($piFlexForm['data'] as $sheet => $data) {
                foreach ($data as $lang => $value) {
                    foreach ($value as $key => $val) {
                        $ff_value = trim($pObj->pi_getFFvalue($piFlexForm, $key, $sheet));
                        if ($ff_value != '') { // do not overwrite conf settings with ''
                            $pObj->conf[$key] = $ff_value;
                            unset($pObj->conf[$key.'.']); // unset stdWrap settings if available
                        }
                    }
                }
            }
        } elseif (!$noExceptionIfNoFlexform) {
             throw new Tx_PtExtbase_Exception_Exception('No plugin configuration found!', 0, 'No flexform data found. Please update your plugin configuration!');
        }

    }



    /***************************************************************************
     *   SECTION: FORMATTING/CONVERSION METHODS
     **************************************************************************/

    /**
     * Filters a given scalar value for HTML output on web pages to prevent XSS attacks and similar hacks.
     * Should be used instead of htmlspecialchars() for any output value in FE plugins.
     * Use Tx_PtExtbase_Div::htmlOutputArray() for arrays or Tx_PtExtbase_Div::htmlOutputArrayAccess() for ArrayAccess objects
     *
     * @param   mixed           string or scalar value to be filtered (mixed type string/integer/float/boolean)
     * @return  string|NULL     filtered value string (empty string if input value was no scalar and not NULL) or NULL if input value was NULL
     * @see     Tx_PtExtbase_Div::htmlOutputArray()
     * @see     Tx_PtExtbase_Div::htmlOutputArrayAccess()
     * @see     http://www.cgisecurity.com/articles/xss-faq.shtml#vendor
     * @author  Rainer Kuhn 
     */
     public static function htmlOutput($value) {

        $filteredValue = '';

        // scalars: convert HTML special chars in filtered value
        if (is_scalar($value)) {
            $filteredValue = htmlspecialchars((string)$value, ENT_QUOTES); // default PHP special char conversion with double AND single quotes translated (translates: & " ' < >)
        // NULL: keep NULL as filtered value
        } elseif (is_null($value)) {
            $filteredValue = NULL;
        // all other values (including objects and arrays): set filtered value to empty string
        } else {
            if (TYPO3_DLOG) t3lib_div::devLog(__METHOD__.'(): unfilterable value has been converted to empty string', 'pt_tools', 2, array('original value' => $value));
        }

        return $filteredValue;

    }


    
    /**
     * Filters the elements of a given array for HTML output on web pages to prevent XSS attacks and similar hacks.
     * Should be used instead of htmlspecialchars() for any array  intended for output in FE plugins.
     *
     * @param   array       array with values to be filtered for output
     * @param   boolean     (optional) flag whether the array keys should be filtered, too (default=1). This is useful if the array keys will be used for HTML output, e.g. in selectorboxes.
     * @return  array       array copy with filtered values (or empty array if given input param was no array)
     * @see     Tx_PtExtbase_Div::htmlOutput()
     * @author  Rainer Kuhn 
     */
     public static function htmlOutputArray($array, $filterKeys=1) {

        $filteredArray = array();

        if (is_array($array)) {

            foreach ($array as $key=>$value) {

            // array key conversion (if requested)
                $newKey = ($filterKeys == 1 ? Tx_PtExtbase_Div::htmlOutput($key) : $key);

            // array value conversion
                // scalars: use default htmlOutput()
                if (is_scalar($value)) {
                    $filteredArray[$newKey] = Tx_PtExtbase_Div::htmlOutput($value);
                // nested arrays: recursive function call
                } elseif (is_array($value)) {
                    $filteredArray[$newKey] = Tx_PtExtbase_Div::htmlOutputArray($value, $filterKeys);
                // objects implementing the ArrayAccess interface: use htmlOutputArrayAccess()
                } elseif ($value instanceof ArrayAccess) {
                    $filteredArray[$newKey] = Tx_PtExtbase_Div::htmlOutputArrayAccess($value, $filterKeys);
                // NULL: keep NULL as filtered value
                } elseif (is_null($value)) {
                    $filteredArray[$newKey] = NULL;
                // all other values (including non-ArrayAccess objects): set filtered value to empty string
                } else {
                    $filteredArray[$newKey] = '';
                    if (TYPO3_DLOG) t3lib_div::devLog(__METHOD__.'(): unfilterable array value of key "'.$key.'" has been converted to empty string', 'pt_tools', 2, array('original value' => $value));
                }
            }

        } else {
            if (TYPO3_DLOG) t3lib_div::devLog(__METHOD__.'(): given parameter was no array', 'pt_tools', 2, array('original parameter' => $array));
        }

        return $filteredArray;

    }
    
    

    /**
     * Filters the elements of a given ArrayAccess object for HTML output on web pages to prevent XSS attacks and similar hacks.
     * Should be used instead of htmlspecialchars() for any ArrayAccess object intended for output in FE plugins.
     *
     * IMPORTANT: since the object will be cloned internally, this method does not work for non-clonable objects (e.g. Singletons).
     * In this case you could implement the tx_pttools_iTemplateable interface to your object and sent the return of the getMarkerArray() method through Tx_PtExtbase_Div::htmlOutputArray.
     *
     * IMPORTANT: since the object will be cloned internally, this method does not work if the reference to this object is important to you
     * or any persistance manager or other fancy stuff you might use.
     *
     * IMPORTANT: ArrayAccess objects usually aren't Arrays for a reason. Applying this method on them (which btw will be applied automatically
     * when passing it to a pt_mvc view if not explicitely preventing this by setting the addItem's third parameter to false) will propably
     * mess your object up. So if you don't want to spend hours debugging strange side effects think twice _before_ you let your object pass this
     * method. Of course having all values htmlspecialchars'ed is always a good thing, but in case of objects it's not always a good idea to do this with
     * a hammer-style method like this one. Think of validating/sanitizing your values before passing it to the view (which is a good idea in any case -
     * and btw: ext:tcaobjects offers a nice and elegant solution to do this) or use Smarty's escape modifier to do this for the object's values when
     * really outputting them {$myObject.evilPropertyAccessedByArrayAccess|escape:"html"}
     *
     * Bye, and have a nice day, Fabrizio
     * (Why do I have the nagging feeling, that noone will ever read this comment?!)
     *
     * @param   ArrayAccess     object implementing the ArrayAccess interface containing property values to be filtered for output
     * @param   boolean         (optional) flag whether keys of eventually contained nested arrays should be filtered, too. See comment of $filterKeys in htmlOutputArray.
     * @return  ArrayAccess     cloned object (implementing the ArrayAccess interface) containing filtered property values
     * @see     Tx_PtExtbase_Div::htmlOutput()
     * @see     Tx_PtExtbase_Div::htmlOutputArray()
     * @author  Rainer Kuhn 
     */
     public static function htmlOutputArrayAccess(ArrayAccess $arrayObject, $filterNestedArrayKeys=1) {

        // prevent endless recursion loop for nested objects
        static $loopCounter = 0;
        $loopCounter += 1;
        if ($loopCounter > 99) {
            throw new Tx_PtExtbase_Exception_Internal('Recursion break', 'Max. recursion depth of 99 exceeded in '.__METHOD__);
        }

        $filteredObject = clone($arrayObject);

        foreach ($filteredObject as $key=>$value) {

            // scalars: use default htmlOutput()
            if (is_scalar($value)) {
                $tmp = Tx_PtExtbase_Div::htmlOutput($value);
                // Write only back to property if something has changed. Writing properties in the ArrayAccess interface can be much much than setting a value...
                if (strcmp($tmp, $value) != 0) {
                    // unset the property first, because overwriting it can have side-effects on ArrayAccess objects (e.g. when checking if an item already exists in the collection)
                    unset($filteredObject[$key]);
                    $filteredObject[$key] = $tmp;
                }
            // arrays: use htmlOutputArray()
            } elseif (is_array($value)) {
                // unset the property first, because overwriting it can have side-effects on ArrayAccess objects (e.g. when checking if an item already exists in the collection)
                unset($filteredObject[$key]);
                $filteredObject[$key] = Tx_PtExtbase_Div::htmlOutputArray($value, $filterNestedArrayKeys);
            // objects implementing the ArrayAccess interface: recursive function call
            } elseif ($value instanceof ArrayAccess) {
                // unset the property first, because overwriting it can have side-effects on ArrayAccess objects (e.g. when checking if an item already exists in the collection)
                unset($filteredObject[$key]);
                $filteredObject[$key] = Tx_PtExtbase_Div::htmlOutputArrayAccess($value, $filterNestedArrayKeys);
            // NULL: keep NULL as filtered value
            } elseif (is_null($value)) {
                // unset the property first, because overwriting it can have side-effects on ArrayAccess objects (e.g. when checking if an item already exists in the collection)
                unset($filteredObject[$key]);
                $filteredObject[$key] = NULL;
            // all other values (including non-ArrayAccess objects): set filtered value to empty string
            } else {
                // unset the property first, because overwriting it can have side-effects on ArrayAccess objects (e.g. when checking if an item already exists in the collection)
                unset($filteredObject[$key]);
                $filteredObject[$key] = '';
                if (TYPO3_DLOG) t3lib_div::devLog(__METHOD__.'(): unfilterable ArrayAccess object property "'.$key.'" has been converted to empty string', 'pt_tools', 2, array('original value' => $value));
            }
        }

        return $filteredObject;

    }

    
    
    /**
     * Returns the given value as an integer (returns 1 for non-numeric values and for negative values if configured with 2nd param)
     *
     * @param   mixed       value to convert to an integer
     * @param   boolean     (optional) flag wether only positive integer should be returned (if set to 1, this method will return 1 for any negative numeric values)
     * @return  integer     integer converted from given value
     * @author  Rainer Kuhn 
     */
     public static function returnIntegerValue($value, $returnOnlyPositive=0) {

        // use integer 1 for everything else than a numeric value
        $intValue = 1;

        // use converted integer value for numeric values
        if (is_numeric(str_replace(',', '.', $value))) {
            $intValue = (integer)$value;
        }

        // return 1 for any negative numeric values if second param is set to true
        if ($returnOnlyPositive == 1 && $intValue < 0) {
            $intValue = 1;
        }

        return $intValue;
    }
    
    

    /**
     * Make sure a comma-separated list of integers is really only that
     *
     * @param   string      comma-separated integer list
     * @return  string      sanitized list
     * @author  Wolfgang Zenker <zenker@punkt.de>
     */
     public static function sanitizeIntList($list) {

        // turn list into array, sanitize array elements, put back into list
        $listArray = explode(',', $list);
        $cleanArray = explode(',', $list);
        foreach ($listArray as $element) {
            $cleanArray[] = intval($element);
        }
        $list = implode(',', $cleanArray);

        return $list;
    }
    
    

    /**
     * Purges a comma seperated list (CSL) string (tries to compensate erroneous entries) and returns exploded string as array
     *
     * @param   string      comma seperated list (CSL) to purge and explode
     * @param   string      (optional) name of exploded array for debug trace output
     * @return  array       exploded elements of CSL
     * @author  Rainer Kuhn 
     */
    public static function returnArrayFromCsl($csl, $arrayName='') {

        // try to compensate erroneous blanks and double commas
        $tmpCsl = str_replace(' ', '', $csl);
        $tmpCsl = str_replace(',,', ',', $tmpCsl);

        // delete comma at string end (if exists)
        if (strrpos($tmpCsl, ',') === strlen($tmpCsl)-1) {
            $tmpCsl = substr($tmpCsl, 0, strlen($tmpCsl)-1);
        }

        // explode and return as array
        $a_csl = array();
        if (!empty($tmpCsl)) {
            if (strpos($tmpCsl, ',') === FALSE) {
                $a_csl[] = $tmpCsl; // if no commas are found: use comlete string as only array element
            } else {
                $a_csl = explode(',', $tmpCsl);
            }
        }

        return $a_csl;

    }
    
    

    /**
     * Returns an associative array of a given string list of separated key-value-pairs
     *
     * @param   string      list of separated key-value-pairs, e.g. 'key1=value1;key2=value2;key2=value2'
     * @param   string      (optional) separator of the key-value pairs within the list (default: ';')
     * @param   string      (optional) separator of key and value within a pair (default: '=')
     * @return  array       associative array of the given key-value list
     * @author  Rainer Kuhn , Fabrizio Branca <mail@fabrizio-branca.de>
     */
    public static function getArrayFromKeyValueList($keyValueList, $pairSeparator=';', $keyValueSeparator='=') {

        $resultArr = array();
        $keyValuePairArr = explode($pairSeparator, $keyValueList);

        foreach ($keyValuePairArr as $keyValuePair){
            list($key, $value) = explode($keyValueSeparator, $keyValuePair);
            $resultArr[trim($key)] = trim($value);
        }

        return $resultArr;

    }
    
    

    /**
     * Converts all string values of a given array from one to another charset encoding. This method requires libiconv to be installed on the server!
     *
     * @param   array       array to convert (only string values will be converted, non-string values will not be changed). NOTE: Binary strings can be excluded from conversion using the $exclusionArr param!
     * @param   string      (optional) charset encoding of the input array (default is 'ISO-8859-1')
     * @param   string      (optional) charset encoding to convert string values to (default is 'UTF-8')
     * @param   array       (optional) array of keys of $inputArray to exclude their values from string conversion - use this param to exclude binary strings from conversion
     * @param   string      (optional) no conversion handling type - possible values are '', '//TRANSLIT' or '//IGNORE' (default is '//TRANSLIT'). For description of these values see http://de.php.net/manual/en/function.iconv.php.
     * @return  mixed       converted array with all strings values converted to requested character encoding OR type and value of 1. param if this was not an array
     * @see     http://de.php.net/iconv
     * @author  Rainer Kuhn 
     */
     public static function iconvArray($inputArray, $inputCharset='ISO-8859-1', $outputCharset='UTF-8', $exclusionArr=array(), $noConvHandling='//TRANSLIT') {

        if (is_array($inputArray)) {

            $outputArray = array();
            foreach ($inputArray as $key=>$value) {
                if (is_string($value) && !in_array($key, $exclusionArr)) {
                    $outputArray[$key] = iconv($inputCharset, $outputCharset.$noConvHandling, $value);
                } elseif (is_array($value) && !in_array($key, $exclusionArr)) {
                    $outputArray[$key] = self::iconvArray($value, $inputCharset, $outputCharset, $exclusionArr, $noConvHandling);
                } else {
                    $outputArray[$key] = $value;
                }
            }
        // "security bottom" if one passes anything other than an array (e.g. a NULL result from a database query fetch assoc call)
        } else {
            $outputArray = $inputArray;
        }

        return $outputArray;

    }
    
    

    /**
     * Encrypts a password with md5 using salt
     *
     * @param   string      cleartext password
     * @param   string      (optional) salt (default: generate random salt)
     * @return  string      encrypted password including salt
     * @author  Wolfgang Zenker <zenker@punkt.de>
     */
    public static function cryptPw($cleartext, $salt='') {

        // create salt
        if ($salt == '') {
            // create random salt
            $salt = chr(rand(48, 122)).chr(rand(48, 122)).chr(rand(48, 122)).chr(rand(48, 122));
        }
        // mark salt as md5 salt
        $salt = '$1$'.$salt.'$';

        return crypt($cleartext, $salt);

    }
    
    

    /**
     * Converts an array to a json string
     *
     * @param   array   $arr
     * @return  string  json
     * @see     http://www.bin-co.com/php/scripts/array2json/
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
	 * @deprecated Use PHP internal function
     */
    public static function array2json($arr) {

        if (function_exists('json_encode')) {
            return json_encode($arr);

        } else {
            $parts = array();
            $is_list = false;

            //Find out if the given array is a numerical array
            $keys = array_keys($arr);
            $max_length = count($arr)-1;
            if(($keys[0] == 0) and ($keys[$max_length] == $max_length)) {//See if the first key is 0 and last key is length - 1
                $is_list = true;
                for($i=0; $i<count($keys); $i++) { //See if each key correspondes to its position
                    if($i != $keys[$i]) { //A key fails at position check.
                        $is_list = false; //It is an associative array.
                        break;
                    }
                }
            }
            foreach($arr as $key=>$value) {
                if(is_array($value)) { //Custom handling for arrays
                    if($is_list) $parts[] = self::array2json($value); /* :RECURSION: */
                    else $parts[] = '"' . $key . '":' . self::array2json($value); /* :RECURSION: */
                } else {
                    $str = '';
                    if(!$is_list) $str = '"' . $key . '":';

                    //Custom handling for multiple data types
                    if(is_numeric($value)) $str .= $value; //Numbers
                    elseif($value === false) $str .= 'false'; //The booleans
                    elseif($value === true) $str .= 'true';
                    else $str .= '"' . addslashes($value) . '"'; //All other things
                    // :TODO: Is there any more datatype we should be in the lookout for? (Object?)

                    $parts[] = $str;
                }
            }
            $json = implode(',',$parts);

            if($is_list) return '[' . $json . ']';//Return numerical JSON
            return '{' . $json . '}';//Return associative JSON
        }

    }



    /***************************************************************************
     *   SECTION: DATABASE RELATED METHODS
     **************************************************************************/

    /**
     * Returns the last built SQL SELECT query with tabs removed.
     *
     * This function tries to retrieve the last built SQL SELECT query from the database object property $this->debug_lastBuiltQuery (works only since T3 3.8.0 with $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true). If this does not succeed, a fallback method is used (for former versions of class.t3lib_db.php [TYPO3 3.6.0-3.8.0beta1] or $GLOBALS['TYPO3_DB']->store_lastBuiltQuery _not_ set to true) to retrieve the query string from t3lib_db::SELECTquery() - as this is an overhead (t3lib_db::SELECTquery() is called a second time after the call from t3lib_db::exec_SELECT_query) IMO this should not be a permanent solution.
     *
     * @param   t3lib_db    TYPO3 database object (instance of t3lib_db) used for last executed SQL query
     * @param   string      select field name(s) passed to last executed SQL query (see comment of t3lib_db::exec_SELECTquery())
     * @param   string      from clause/table name(s) passed to last executed SQL query (see comment of t3lib_db::exec_SELECTquery())
     * @param   string      where clause passed to last executed SQL query (see comment of t3lib_db::exec_SELECTquery())
     * @param   string      (optional) order by clause passed to last executed SQL query (see comment of t3lib_db::exec_SELECTquery())
     * @param   string      (optional) group by clause passed to last executed SQL query (see comment of t3lib_db::exec_SELECTquery())
     * @param   string      (optional) limit clause passed to last executed SQL query (see comment of t3lib_db::exec_SELECTquery())
     * @return  string      last built SQL query with tabs removed
     * @see                 class.t3lib_db.php, t3lib_db::exec_SELECTquery()
     * @author  Rainer Kuhn 
     */
    public static function returnLastBuiltSelectQuery(t3lib_db $dbObject, $select_fields, $from_table, $where_clause, $groupBy='', $orderBy='', $limit='') {

        // try to get query from debug_lastBuiltQuery (works only for T3 3.8.0 with $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true)
        $query = $dbObject->debug_lastBuiltQuery;

        // fallback for former versions of class.t3lib_db.php (TYPO3 3.6.0-3.8.0beta1) or $GLOBALS['TYPO3_DB']->store_lastBuiltQuery _not_ set to true
        if (strlen($query) < 1) {
            $query = $dbObject->SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
        }

        // remove tabs and return query string
        return str_replace(chr(9), '', $query);

    }

    
    
    /**
     * Returns the last built SQL DELETE query with tabs removed.
     *
     * This function tries to retrieve the last built SQL DELETE query from the database object property $this->debug_lastBuiltQuery (works only since T3 3.8.0 with $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true). If this does not succeed, a fallback method is used (for former versions of class.t3lib_db.php [TYPO3 3.6.0-3.8.0beta1] or $GLOBALS['TYPO3_DB']->store_lastBuiltQuery _not_ set to true) to retrieve the query string from t3lib_db::DELETEquery() - as this is an overhead (t3lib_db::DELETEquery() is called a second time after the call from t3lib_db::exec_SELECT_query) IMO this should not be a permanent solution.
     *
     * @param   t3lib_db    TYPO3 database object (instance of t3lib_db) used for last executed SQL query
     * @param   string      from clause/table name passed to last executed SQL query (see comment of t3lib_db::exec_DELETEquery())
     * @param   string      where clause passed to last executed SQL query (see comment of t3lib_db::exec_DELETEquery())
     * @return  string      last built SQL query with tabs removed
     * @see                 class.t3lib_db.php, t3lib_db::exec_DELETEquery()
     * @author  Rainer Kuhn 
     */
    public static function returnLastBuiltDeleteQuery(t3lib_db $dbObject, $from_table, $where_clause) {

        // try to get query from debug_lastBuiltQuery (works only for T3 3.8.0 with $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true)
        $query = $dbObject->debug_lastBuiltQuery;

        // fallback for former versions of class.t3lib_db.php (TYPO3 3.6.0-3.8.0beta1) or $GLOBALS['TYPO3_DB']->store_lastBuiltQuery _not_ set to true
        if (strlen($query) < 1) {
            $query = $dbObject->DELETEquery($from_table, $where_clause);
        }

        // remove tabs and return query string
        return str_replace(chr(9), '', $query);

    }
    
    

    /**
     * Returns the last built SQL UPDATE query with tabs removed.
     *
     * This function tries to retrieve the last built SQL UPDATE query from the database object property $this->debug_lastBuiltQuery (works only since T3 3.8.0 with $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true). If this does not succeed, a fallback method is used (for former versions of class.t3lib_db.php [TYPO3 3.6.0-3.8.0beta1] or $GLOBALS['TYPO3_DB']->store_lastBuiltQuery _not_ set to true) to retrieve the query string from t3lib_db::UPDATEquery() - as this is an overhead (t3lib_db::UPDATEquery() is called a second time after the call from t3lib_db::exec_UPDATE_query) IMO this should not be a permanent solution.
     *
     * @param   t3lib_db    TYPO3 database object (instance of t3lib_db) used for last executed SQL query
     * @param   string      table name passed to last executed SQL query (see comment of t3lib_db::exec_UPDATEquery())
     * @param   string      where clause passed to last executed SQL query (see comment of t3lib_db::exec_UPDATEquery())
     * @param   array       array containing field/value-pairs passed to last executed SQL query (see comment of t3lib_db::exec_UPDATEquery())
     * @return  string      last built SQL query with tabs removed
     * @see                 class.t3lib_db.php, t3lib_db::exec_UPDATEquery()
     * @author  Rainer Kuhn 
     */
    public static function returnLastBuiltUpdateQuery(t3lib_db $dbObject, $table, $where, $updateFieldsArr) {

        // try to get query from debug_lastBuiltQuery (works only for T3 3.8.0 with $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true)
        $query = $dbObject->debug_lastBuiltQuery;

        // fallback for former versions of class.t3lib_db.php (TYPO3 3.6.0-3.8.0beta1) or $GLOBALS['TYPO3_DB']->store_lastBuiltQuery _not_ set to true
        if (strlen($query) < 1) {
            $query = $dbObject->UPDATEquery($table, $where, $updateFieldsArr);
        }

        // remove tabs and return query string
        return str_replace(chr(9), '', $query);

    }
    
    

    /**
     * Returns the last built SQL INSERT query with tabs removed.
     *
     * This function tries to retrieve the last built SQL INSERT query from the database object property $this->debug_lastBuiltQuery (works only since T3 3.8.0 with $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true). If this does not succeed, a fallback method is used (for former versions of class.t3lib_db.php [TYPO3 3.6.0-3.8.0beta1] or $GLOBALS['TYPO3_DB']->store_lastBuiltQuery _not_ set to true) to retrieve the query string from t3lib_db::INSERTquery() - as this is an overhead (t3lib_db::INSERTquery() is called a second time after the call from t3lib_db::exec_INSERT_query) IMO this should not be a permanent solution.
     *
     * @param   t3lib_db    TYPO3 database object (instance of t3lib_db) used for last executed SQL query
     * @param   string      table name passed to last executed SQL query (see comment of t3lib_db::exec_INSERTquery())
     * @param   array       array containing field/value-pairs passed to last executed SQL query (see comment of t3lib_db::exec_INSERTquery())
     * @return  string      last built SQL query with tabs removed
     * @see                 class.t3lib_db.php, t3lib_db::exec_INSERTquery()
     * @author  Rainer Kuhn 
     */
    public static function returnLastBuiltInsertQuery(t3lib_db $dbObject, $table, $insertFieldsArr) {

        // try to get query from debug_lastBuiltQuery (works only for T3 3.8.0 with $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true)
        $query = $dbObject->debug_lastBuiltQuery;

        // fallback for former versions of class.t3lib_db.php (TYPO3 3.6.0-3.8.0beta1) or $GLOBALS['TYPO3_DB']->store_lastBuiltQuery _not_ set to true
        if (strlen($query) < 1) {
            $query = $dbObject->INSERTquery($table, $insertFieldsArr);
        }

        // remove tabs and return query string
        return str_replace(chr(9), '', $query);

    }

    
    
    /**
     * This function adds fieldentries for crdate, tstamp, cruser_id and pid to the fieldValue array used for SQL INSERT and UPDATE statements.
     *
     * crdate, cruser_id and pid are only added if INSERT is set to true.
     *
     * @param   array       array which contains the field Value pairs for INSERT or UPDATE statements
     * @param   boolean     if true array will be used for SQL INSERT statement
     * @param   integer     (optional) page id for INSERT (this setting has no effect if 2. param is set to false)
     * @param   integer     (optional) BE user id for INSERT (this setting has no effect if 2. param is set to false)
     * @return  array       expanded array for SQL INSERT or UPDATE statements
     * @see                 class.t3lib_db.php, t3lib_db::exec_INSERTquery(), t3lib_db::exec_UPDATEquery()
     * @author  Dorit Rottner <rottner@punkt.de>
     */
    public static function expandFieldValuesForQuery($fieldValueArr, $isInsert=false, $pid=NULL, $cruser_id=NULL) {

        if ($isInsert) {
            $fieldValueArr['crdate'] = time();
            if (! is_null($pid)) {
                $fieldValueArr['pid'] = intval($pid);
            }
            if (! is_null($cruser_id)) {
                $fieldValueArr['cruser_id'] = intval($cruser_id);
            }
        }
        $fieldValueArr['tstamp'] = time();

        return $fieldValueArr;

    }
    
    

    /**
     * Returns the TYPO3_MODE (FE/BE) related TYPO3 'enable fields' check clause as a where-clause addition for a given table
     *
     * @param   string      table name
     * @param   string      (optional) table alias
     * @return  string      where-clause addition for this table
     * @author  Dorit Rottner <rottner@punkt.de>
     */
    public static function enableFields($table, $alias = '') {

        if (TYPO3_MODE == 'FE' && is_object($GLOBALS['TSFE']->cObj)) {
            $result =  $GLOBALS['TSFE']->cObj->enableFields($table);
        } else {
            require_once PATH_t3lib.'class.t3lib_befunc.php';
            $result = t3lib_BEfunc::BEenableFields($table);
            // this is a bugfix for TYPO3 because if there are no hidden, start and endtime fields it returns AND
            if (trim($result) == 'AND') {
                $result = '';
            }
            $result .= t3lib_BEfunc::deleteClause($table);
        }
        if ($alias != '') {
            $search = $table.'.';
            $replace = $alias.'.';
            $result = str_replace($search, $replace, $result);
        }
        return $result;

    }

    
    
    /**
     * Returns true if a specified database table exists in the given database
     *
     * @param   string      database table name to check
     * @param   t3lib_db    database object of type t3lib_db to use (e.g. $GLOBALS['TYPO3_DB'] to use TYPO3 default database)
     * @return  boolean     TRUE if table exists in specified database, FALSE if not
     * @throws  Tx_PtExtbase_Exception_Exception   if the SHOW TABLES query fails/returns false
     * @author  Rainer Kuhn 
     */
    public static function dbTableExists($table, t3lib_db $dbObj) {

        $tableExists = false;
        $query  = 'SHOW TABLES';

        // exec query using TYPO3 DB API
        $res = $dbObj->sql_query($query);
        if ($res == false) {
            throw new Tx_PtExtbase_Exception_Exception('Query failed', 1, $dbObj->sql_error());
        }

        // store all tables in an array
        $a_tables = array();
        while ($a_row = $dbObj->sql_fetch_assoc($res)) {
            $a_tables[] = current($a_row);
        }
        $dbObj->sql_free_result($res);

        // search specified table in array
        if (in_array($table, $a_tables)) {
            $tableExists = true;
        }

        return $tableExists;

    }

    
    
    /**
     * Returns true if a specified database table contains record rows: the existence of a table ashould have been checked before using Tx_PtExtbase_Div::dbTableExists()!
     *
     * @param   string      database table name to check
     * @param   string      database name of the table to check
     * @param   t3lib_db    database object of type t3lib_db to use (e.g. $GLOBALS['TYPO3_DB'] to use TYPO3 default database)
     * @return  boolean     TRUE if specified table contains record rows, FALSE if not
     * @throws  Tx_PtExtbase_Exception_Exception   if the SHOW TABLE STATUS query fails/returns false
     * @author  Rainer Kuhn 
     */
    public static function dbTableHasRecords($table, $dbName, t3lib_db $dbObj) {

        $tableHasRecords = false;
        $query  = 'SHOW TABLE STATUS FROM '.$dbObj->quoteStr($dbName, $table).' LIKE "'.$dbObj->quoteStr($table, $table).'"';

        // exec query using TYPO3 DB API
        $res = $dbObj->sql_query($query);
        if ($res == false) {
            throw new Tx_PtExtbase_Exception_Exception('Query failed', 1, $dbObj->sql_error());
        }
        $a_row = $dbObj->sql_fetch_assoc($res);
        $dbObj->sql_free_result($res);

        // check number of table rows
        if ($a_row['Rows'] > 0) {
            $tableHasRecords = true;
        }

        return $tableHasRecords;
    }


	/**
	 * @static
	 * @throws Exception if file not found
	 * @param $filePath string path to typoscript file
	 * @return array ts-Config
	 */
	public static function loadTypoScriptFromFile($tsSetupFilePath) {

		if(!file_exists($tsSetupFilePath)) Throw new Exception('No Typoscript file found at path ' . $tsSetupFilePath . ' 1316733309');

		$rawTsConfig  = file_get_contents($tsSetupFilePath);
		$tsParser  = t3lib_div::makeInstance('t3lib_TSparser'); /** @var $tsParser  t3lib_TSparser */

		$tsLines = explode(LF, $rawTsConfig);

		foreach ($tsLines as &$value) {
			$includeData = t3lib_TSparser::checkIncludeLines($value, 1, TRUE);
			$value = $includeData['typoscript'];
		}

		$rawTsConfig = implode(LF, $tsLines);

		$tsParser->parse($rawTsConfig);
		$tsArray = $tsParser->setup;

		return $tsArray;
	}



	/**
	 * Especially when sending an object, that is marked as lazy loading to a viewHelper,
	 * the real instance of this object must be received before it is send to the viewHelper
	 * to fit to the viewHelpers class signature.
	 *
	 * @param $object
	 * @return mixed
	 */
	public static function getLazyLoadedObject($object) {
		if (get_class($object) === 'Tx_Extbase_Persistence_LazyLoadingProxy'
				|| get_class($object) === 'TYPO3\\CMS\\Extbase\\Persistence\\Generic\\LazyLoadingProxy') {
			return $object->_loadRealInstance();
		} else {
			return $object;
		}
	}


}

?>
