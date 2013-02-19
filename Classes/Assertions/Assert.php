<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 - 2011 Fabrizio Branca, Michael Knoll
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
 * Assertion class
 *
 * @see     http://www.debuggable.com/posts/assert-the-yummyness-of-your-cake:480f4dd6-7fe0-4113-9776-458acbdd56cb
 * @author  Fabrizio Branca
 * @author  Michael Knoll
 * @see Tx_PtExtbase_Tests_Unit_Assertions_AssertTest
 */
class Tx_PtExtbase_Assertions_Assert {

	/**
	 * Holds instance of t3lib_DB that can be mocked and injected for testing
	 *
	 * @var t3lib_DB
	 */
	public static $dbObj = NULL;



    /**
     * Basic test method
     *
     * @param   mixed   first parameter
     * @param   mixed   second parameter
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @param   bool    (optional) if true (default), parameters are tested by identy and not only equality
     * @param   int     (optional) error code, default is 0
     * @throws  Exception   if assertion fails
     */
    public static function test($val, $expected, array $info = array(), $strict = true) {

        // check values
        $success = ($strict) ? $val === $expected : $val == $expected;

        if ($success) {
            return;
        }

        // values do not match, preparing exception...

        $calls = debug_backtrace();
        foreach ($calls as $call) {
            if ($call['file'] !== __FILE__) {
                $assertCall = $call;
                break;
            }
        }
        $triggerCall = current($calls);


        $info = array_merge(
            array(
                'file'         => $assertCall['file'],
                'line'         => $assertCall['line'],
                'function'     => $triggerCall['class'] . '::' . $triggerCall['function'],
                'assertType'   => $assertCall['function'],
                'val'          => $val,
                'expected'     => $expected,
            ),
            $info
        );

        $debugMessage = '';
        foreach ($info as $key => $value) {
            $debugMessage .= sprintf('<span class="label">%1$s</span><span class="value %1$s">%2$s</span>', $key, $value);
        }
        $debugMessage = trim($debugMessage, ' ,');

        $exception = new Tx_PtExtbase_Exception_Assertion('Assertion "'.$assertCall['function'].'" failed! '.$info['message'], $debugMessage);
        $exception->setFile($assertCall['file']);
        $exception->setLine($assertCall['line']);
        if ($info['permanent']) {
            $exception->setPermanent();
        }
        throw $exception;
    }



    /**
     * Test if value is true
     *
     * @param   mixed   value
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isTrue($val, array $info = array()) {

        return self::test($val, true, $info);
    }



    /**
     * Test if value if false
     *
     * @param   mixed   value
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isFalse($val, array $info = array()) {

        return self::test($val, false, $info);
    }



    /**
     * Test if two values are equal
     *
     * @param   mixed   $a
     * @param   mixed   $b
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isEqual($a, $b, array $info = array()) {
        if ($info['message']) {
            $info['message'] = sprintf($info['message'], $a, $b);
        }
        return self::test($a, $b, $info, false);
    }



    /**
     * Test if two values are not equal
     *
     * @param   mixed   $a
     * @param   mixed   $b
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isNotEqual($a, $b, array $info = array()) {

        return self::test($a == $b, false, $info, true);
    }



    /**
     * Test if two values are identical
     *
     * @param   mixed   $a
     * @param   mixed   $b
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isIdentical($a, $b, array $info = array()) {

        return self::test($a, $b, $info, true);
    }



    /**
     * Test if two values are not identical
     *
     * @param   mixed   $a
     * @param   mixed   $b
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isNotIdentical($a, $b, array $info = array()) {

        return self::test($a === $b, false, $info, true);
    }



    /**
     * Test if a value matches a reqular expression
     *
     * @param   string  pattern
     * @param   string  value
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function matchesPattern($pattern, $val, array $info = array()) {
        self::isString($pattern);
        self::isString($val);
        return self::test(preg_match($pattern, $val), true, $info, false);
    }
        
    
    
    /**
     * Test if variable consists only of letters and digits
     *
     * @param   mixed   value
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isAlphaNum($val, array $info = array()) {
        return self::matchesPattern('/^[\w\d]+$/', $val, $info);
    } 


    /**
     * Test if a this is a valid email
     *
     * @param   string  email
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isValidEmail($email, array $info = array()) {
        self::isString($email);
        return self::test(t3lib_div::validEmail($email), true, $info);
    }

    /**
     * Test if variable is empty
     *
     * @param   mixed   value
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isEmpty($val, array $info = array()) {

        return self::test(empty($val), true, $info);
    }



    /**
     * test if variable is not empty
     *
     * @param   mixed   value
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isNotEmpty($val, array $info = array()) {

        return self::test(empty($val), false, $info);
    }



    /**
     * Test if value is numeric
     *
     * @param   mixed   value
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isNumeric($val, array $info = array()) {

        return self::test(is_numeric($val), true, $info);
    }



    /**
     * Test if value is not numeric
     *
     * @param   mixed   value
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isNotNumeric($val, array $info = array()) {

        return self::test(is_numeric($val), false, $info);
    }



    /**
     * Test if value is an integer
     *
     * @param   mixed   value
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isInteger($val, array $info = array()) {

        return self::test(is_int($val), true, $info);
    }

    /**
     * Test if a value is a positive integer (allowing zero or not, depending on 2nd param)
     *
     * @param   mixed   value
     * @param   bool    (optional) allow "0", default is false
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isPositiveInteger($val, $allowZero = false, array $info = array()) {
        
        $info['tested_value'] = $val;
        $info['value_type'] = gettype($val);
        $info['zero_allowed'] = $allowZero ? 'true' : 'false';
        
        return self::test((is_int($val) && (intval($val) >= ($allowZero ? 0 : 1))), true, $info);
        
    }

    /**
     * Test if value is not an integer
     *
     * @param   mixed   value
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isNotInteger($val, array $info = array()) {

        return self::test(is_int($val), false, $info);
    }



    /**
     * Test if value is integerish
     *
     * @param   mixed   value
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isIntegerish($val, array $info = array()) {

        return self::test(is_int($val) || ctype_digit($val), true, $info);
    }



    /**
     * Test if value is not integerish
     *
     * @param   mixed   value
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isNotIntegerish($val, array $info = array()) {

        return self::test(is_int($val) || ctype_digit($val), false, $info);
    }



    /**
     * Test if value is an object
     *
     * @param   mixed   value
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isObject($val, array $info = array()) {

        return self::test(is_object($val), true, $info);
    }



    /**
     * Test if value is not an object
     *
     * @param   mixed   value
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isNotObject($val, array $info = array()) {

        return self::test(is_object($val), false, $info);
    }



    /**
     * Test if value is boolean
     *
     * @param   mixed   value
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isBoolean($val, array $info = array()) {

        return self::test(is_bool($val), true, $info);
    }



    /**
     * Test if value is not boolean
     *
     * @param   mixed   value
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isNotBoolean($val, array $info = array()) {

        return self::test(is_bool($val), false, $info);
    }



    /**
     * Test if value is a string
     *
     * @param   mixed   value
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isString($val, array $info = array()) {

        return self::test(is_string($val), true, $info);
    }



    /**
     * Test if value is not a string
     *
     * @param   mixed   value
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isNotString($val, array $info = array()) {

        return self::test(is_string($val), false, $info);
    }



    /**
     * Test if value is an array
     *
     * @param   mixed   value
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isArray($val, array $info = array()) {

        return self::test(is_array($val), true, $info);
    }
    
    
    
    /**
     * Test if value is an associative array
     * 
     * @param   mixed   $val    Value to be tested
     * @param   array   $info   Array of information
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isAssociativeArray($val, array $info = array()) {
        
        return self::test(Tx_PtExtbase_Div::isAssociativeArray($val), true, $info);
        
    }



    /**
     * Test if value is not an array
     *
     * @param    mixed    value
     * @param     array    (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isNotArray($val, array $info = array()) {

        return self::test(is_array($val), false, $info);
    }
    
    
    
    /**
     * Test if a value is a non-empty array
     *
     * @param   mixed   value
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     */
    public static function isNotEmptyArray($val, array $info = array()) {
        
        return self::test((is_array($val) && count($val)) > 0, true, $info);
        
    }



    /**
     * Test if a value is in an array
     *
     * @param     mixed    value
     * @param     array     array
     * @param     array    (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isInArray($val, array $array, array $info = array()) {

        return self::test(in_array($val, $array), true, $info);
    }



    /**
     * Test if a value is in an array key
     *
     * @param     mixed    value
     * @param     array     array
     * @param     array    (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isArrayKey($val, array $array, array $info = array()) {

        return self::test(array_key_exists($val, $array), true, $info);
    }



    /**
     * Test if a value is in a comma separated list
     *
     * @param     string    value
     * @param     string     list
     * @param     array    (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isInList($val, $list, array $info = array()) {

        return self::test(t3lib_div::inList($list, $val), true, $info);
    }



    /**
     * Test if a value is in a range
     *
     * @param     mixed    value
     * @param     mixed     lower boundary
     * @param     mixed     higher boundary
     * @param     array    (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isInRange($val, $low, $high, array $info = array()) {
        $info['tested_value'] = $val;
        $info['range_low'] = $low;
        $info['range_high'] = $high;
        return self::test(($val >= $low && $val <= $high), true, $info);
    }



    /**
     * Test if a value is a valid uid for TYPO3 records. (positive integer)
     *
     * @param     mixed    value
     * @param     bool    (optional) allow "0", default is false
     * @param     array    (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isValidUid($val, $allowZero = false, array $info = array()) {
        
        // TODO: test if is_string or is_int
        
        // TODO: replace by regex? Test what is faster...
        $str = strval($val);
        return self::test(ctype_digit($str) && (strlen($str) == 1 || $str[0] != '0') && (intval($val) >= ($allowZero ? 0 : 1)), true, $info);
    }



    /**
     * Test if a value is an array with valid uids for TYPO3 records. (positive integer)
     *
     * @param     mixed    value
     * @param     bool    (optional) allow "0", default is false
     * @param     array    (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isValidUidArray($val, $allowZero = false, array $info = array()) {
        self::isArray($val, $info);
        
        foreach ($val as $uid) {
            self::isValidUid($uid, $allowZero, $info);
        }
    }



    /**
     * Test if value is a valid mysql ressource
     *
     * @param     mixed        value
     * @param     t3lib_DB    (optional) t3lib_DB used, default is NULL, then $GLOBALS['TYPO3_DB'] will be used
     * @param     array        (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isMySQLRessource($res, t3lib_DB $dbObj = NULL, array $info = array()) {
        if (is_null($dbObj)) {
            $dbObj = $GLOBALS['TYPO3_DB'];
        }
        self::isInstanceOf($dbObj, 't3lib_DB', $info);
        
        // append sql_error to info array
        $info['sql_error'] = $dbObj->sql_error();

        if (empty($info['message'])) {
            $info['message'] = $info['sql_error'];
        }
        
        // append debug_lastBuiltQuery to info array
        if (!empty($dbObj->debug_lastBuiltQuery)) {
            $info['debug_lastBuiltQuery'] = $dbObj->debug_lastBuiltQuery; 
        }

		if($res === TRUE || self::test($dbObj->debug_check_recordset($res), true, $info)) {
			return TRUE;
		}
    }
    
    
    
    /**
     * Test if an object is instance of a class or interface
     *
     * @deprecated     use self::isInstanceOf instead!
     * @param     mixed    value
     * @param     string    type
     * @param     array    (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isType($val, $type, array $info = array()) {
        self::isNotEmptyString($type, $info);
        
        return self::test($val instanceof $type, true, $info, true);
    }
    
    
    
    /**
     * Test if the value is a string that is not empty
     *
     * @param     mixed    value
     * @param     array    (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isNotEmptyString($val, array $info = array()) {
        
        return self::test(is_string($val) && (strlen($val)>0), true, $info);
    }
    
    
    
    /**
     * Test if a value is a valid and existing file
     *
     * @param     string    value
     * @param     array    (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isFilePath($val, array $info = array()) {
        self::isNotEmptyString($val, $info);
        
        if ($info['message']) {
            $info['message'] = sprintf($info['message'], $val);
        }
        
        $filePath = t3lib_div::getFileAbsFileName($val);
        return self::test(t3lib_div::validPathStr($filePath) && is_file($filePath), true, $info);        
    }
    
    
    
    /**
     * Test if a value is a valid and existing directory
     *
     * @param     string    value
     * @param     array    (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     * @throws  Tx_PtExtbase_Exception_Assertion   if assertion fails
     */
    public static function isDir($val, array $info = array()) {
        self::isNotEmptyString($val, $info);
        
        $filePath = t3lib_div::getFileAbsFileName($val, false);
        return self::test(t3lib_div::validPathStr($filePath) && is_dir($filePath), true, $info);        
    }

    

    /**
     * Test for two variables being references to each other
     *
     * @param     mixed     first variable
     * @param     mixed     second variable
     * @param     array    (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     */
    public static function isReference(&$a, &$b, array $info = array()) {
        
        if (is_object($a)) {
            $is_ref = ($a === $b);
        } else {
            $temp = $a;
            $a = uniqid('test');
            $is_ref = ($a === $b);
            $a = $temp;
        }
        return self::test($is_ref, true, $info);
    }
    
    
    
    /**
     * Test if an object is instance of a given class/interface
     *
     * @param     mixed    object
     * @param     mixed    class name
     * @param     array    (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     */
    public static function isInstanceOf($object, $class, array $info = array()) {
        self::isObject($object, $info);
        self::isNotEmptyString($class, $info);
        
        $info['class'] = $class;
        if (empty($info['message'])) {
            $info['message'] = sprintf('Object is not an instance of class "%s"!', $class);
        }
        return self::test($object instanceof $class, true, $info);
    }
    
    
    
    /**
     * Test if an object is a non-empty object collection of type objectCollection
     *
     * @param   mixed   object
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     */
    public static function isNotEmptyObjectCollection($object, array $info = array()) {
        
        self::isInstanceOf($object, 'Tx_PtExtbase_Collection_ObjectCollection');
        
        return self::test(count($object) > 0, true, $info);
        
    }
    
    
    
    /**
     * Test if a variable is not null
     *
     * @param    mixed    value 
     * @param    array    (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     */
    public static function isNotNull($val, array $info = array()) {
        return self::test(is_null($val), false, $info);
    }
    
    
    
    /**
     * Test if a variable is null
     *
     * @param   mixed   value 
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     */
    public static function isNull($val, array $info = array()) {
        return self::test(is_null($val), true, $info);
    }
    
    
    
    /**
     * Test if a fe_user is logged in
     *
     * @param   array   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     */
    public static function loggedIn(array $info = array()) {
        return self::test($GLOBALS['TSFE']->loginUser, true, $info, false);
    }
    
    
    
    /**
     * Test if a variable is the name of a table defined in TCA
     *  
     * @param $val
     * @param $info   (optional) additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
     */
    public static function isTcaTable($val, array $info = array()) {
        self::isNotEmptyString($val, $info);
        return self::isArrayKey($val, $GLOBALS['TCA'], $info);
    }



	/**
	 * Test if given object implements given class
	 *
	 * Alias for isInstanceOf
	 *
	 * @param mixed $object Object to test whether it implements given class
	 * @param string $className Class name which object should implement
	 * @param array $info additional info, will be displayed as debug message, if object does not implement given class
	 */
	public static function isA($object, $className, $info=array()) {
		self::classExists($className, array('message' => 'Assertion whether an object implements a class cannot be made, without the target class being loaded. Class ' . $className . ' does not exist! 1356205830'));
		self::isInstanceOf($object, $className, $info);
	}



	/**
	 * Test if class for given class name exists
	 *
	 * @param string $className Name of class to test whether it exists
	 * @param array $info additional info, will be displayed as debug message, if class does not exist
	 */
	public static function classExists($className, $info=array()) {
		self::isTrue(class_exists($className), $info);
	}



	/**
	 * Tests if a given table exists in current default database
	 *
	 * @param string $tablename Table name for which to check whether it exists
	 * @param array $info Additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
	 */
	public static function tableExists($tablename, array $info = array()) {
		self::initializeDbObj();
		$tables = self::$dbObj->admin_get_tables();
		return self::isArrayKey($tablename, $tables, $info);
	}



	/**
	 * Tests if a given table contains a given field.
	 *
	 * @param string $tablename Table name for which to check whether a given field exists in
	 * @param string $fieldname Field name for which to check whether it exists in the given table
	 * @param array $info Additional info, will be displayed as debug message, if a key "message" exists this will be appended to the error message
	 */
	public static function tableAndFieldExist($tablename, $fieldname, $info =  array()) {
		self::initializeDbObj();
		return self::isArrayKey($fieldname, self::$dbObj->admin_get_fields($tablename), $info);
	}



	/**
	 * Tests if an extension is loaded with an optionally given version.
	 *
	 * @param string $extensionKey Extension key of the extension that we want to be loaded
	 * @param string $version Version of the extension that we want to have loaded
	 */
	public static function extensionIsLoaded($extensionKey, $version = '0.0.0') {
		// Check whether extension is loaded at all
		self::isTrue(t3lib_extMgm::isLoaded($extensionKey), array('message' => 'Extension ' . $extensionKey . ' is not loaded!'));

		// Check whether extension is loaded with required version
		list($sanitizedVersion,) = explode('-', $version);
		$loadedVersion = t3lib_extMgm::getExtensionVersion($extensionKey);
		self::test(version_compare($sanitizedVersion, $loadedVersion) >= 0, true, array('message' => 'Extension ' . $extensionKey . ' was installed with version ' . $loadedVersion . ' but version ' . $version . ' is required!'));
	}



	private function initializeDbObj() {
		if (self::$dbObj === NULL) {
			self::$dbObj = $GLOBALS['TYPO3_DB'];
		}
	}

}
