<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2005-2011 Rainer Kuhn, Fabrizio Branca, Michael Knoll
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
 * General exception class derived from PHP's default Exception class
 *
 * @author      Rainer Kuhn
 * @author      Fabrizio Branca 
 * @author      Michael Knoll
 * @package     Exception
 */
class Tx_PtExtbase_Exception_Exception extends Exception {
    
    /*
    // Dev Info: Class structure of parent class (PHP5's default Exception):
    
    class Exception {
        protected $message = 'Unknown exception';   // exception message
        protected $code = 0;                        // user defined exception code
        protected $file;                            // source filename of exception
        protected $line;                            // source line of exception
    
        function __construct($message = null, $code = 0);
    
        final function getMessage();                // message of exception 
        final function getCode();                   // code of exception
        final function getFile();                   // source filename
        final function getLine();                   // source line
        final function getTrace();                  // an array of the backtrace()
        final function getTraceAsString();          // formated string of trace
    
        // Overrideable
        function __toString();                      // formatted string for display
    }
    */    
	
	
	
	/**
     * @const   integer     constant for database error exception
     */
    const EXCP_DATABASE = 1;
    
    
    
    /**
     * @const   integer     constant for configuration error exception
     */
    const EXCP_CONFIG = 2;
    
    
    
    /**
     * @const   integer     constant for internal error exception
     */
    const EXCP_INTERNAL = 3;

    
    
    /**
     * @const   integer     constant for authentication error exception
     */
    const EXCP_AUTH = 4;
    
    
    
    /**
     * @const   integer     constant for webservice error exception
     */
    const EXCP_WEBSERVICE = 5;

	
	
    /**
     * @var     string      additional detailed debug message
     */
    protected $debugMsg = '';
    
    
    
    /**
     * @var     string      error type name (depending on error code param passed to constructor)
     */
    protected $errType = '';
    
    
    
    /**
     * @var     bool        If this exception is permanent you can set this property. 
     *                      pt_mvc's exceptions handling will then send an 404 status code instead of a 503 to prevent
     *                      bots to retry accessing this page  
     */
    protected $permanent = false;
    
    
     
    /**
     * Class constructor: sets internal properties and calls the parent constructor (Exception::__construct(...)
     * 
     * @param   string    optional error message (used for frontend/enduser display, too)    
     * @return  integer   DEPRECATED: optional error code, see EXCP_* class constants (currently: 1=DATABASE ERR, 2=CONFIG ERR, 3=INTERNAL ERR, 4=AUTH ERR, 5=WEBSERVICE ERR) - DEPRECATED for public usage: use special exception classes in res/objects/exceptions/ instead!
     * @param   string    optional detailed debug message (not used for frontend display). For database errors (error code 1) the last TYPO3 DB SQL error is set to the debug message by default. To suppress this or to trace another DB object's SQL error use the third param to replace this default.
     */
    public function __construct($errMsg='', $errCode=0, $debugMsg='') {
        $this->debugMsg = $debugMsg;
        
        // handle different error types ("old" switch structure remains for backwards compatibility)
        switch ($errCode) {
            case self::EXCP_DATABASE:
                $this->errType = 'DATABASE ERROR';
                break;
            case self::EXCP_CONFIG:
                $this->errType = 'CONFIGURATION ERROR';
                break;
            case self::EXCP_INTERNAL:
                $this->errType = 'INTERNAL ERROR';
                break;
            case self::EXCP_AUTH:
                $this->errType = 'AUTHENTICATION ERROR';
                break;
            case self::EXCP_WEBSERVICE:
                $this->errType = 'WEBSERVICE ERROR';
                break;
            default:
                $this->errType = 'ERROR';
                break;
        }
        
        // write to devlog
        if (TYPO3_DLOG) {
            t3lib_div::devLog(
                $this->getMessage(), 
                'pt_extbase', 
                1, // "notice"
                array(
                    'exceptionClass' => get_class($this), 
                    'debugMsg' => $this->debugMsg, 
                    'file' => $this->getFile(), 
                    'line' => $this->getLine(), 
                    'code' => $this->getCode(),
                    'trace' => $this->getTraceAsString(),
                )
            );
        }
        
        // call parent constructor to make sure everything is assigned properly
        parent::__construct($errMsg, $errCode);
        
    }
    
    
    
    /**
     * Custom string representation of the object - can be used for frontend/enduser display
     *
     * @param   void       
     * @return  string      Error type and error display message
     */
    public function __toString() {
        
        $displayString = '[' . $this->errType.(!empty($this->message) ? ': '.$this->message : '!') . ']';
        return $displayString;
        
    }
    
    
    
    /**
     * Handles an exception: Debug information is written to TYPO3 devlog, TYPO3 syslog, TYPO3 TS log and is sent to trace()
     *
     * @param   void       
     * @return  void
     */
    public function handle() {
        
        $traceString = 
            'Error Type     : '.$this->errType.chr(10).
            'Exception Class: '.get_class($this).chr(10).
            'Error Message  : '.$this->getMessage().chr(10).
            (!empty($this->debugMsg) ? 'Debug Message  : '.$this->debugMsg.chr(10) : '').
            'Stack Trace    : '.chr(10).$this->getTraceAsString().chr(10).chr(10)
            ;
        
        // write to TYPO3 devlog
        if (TYPO3_DLOG) {
            t3lib_div::devLog(
                $this->getMessage(), 
                'pt_extbase', 
                3, // "error"
                array(
                    'exceptionClass' => get_class($this), 
                    'debugMsg' => $this->debugMsg, 
                    'file' => $this->getFile(), 
                    'line' => $this->getLine(), 
                    'code' => $this->getCode(),
                    'trace' => $this->getTraceAsString(),
                )
            );
        }
        
        // write to TYPO3 syslog
        $debugMsg = $this->debugMsg ? ': '.$this->debugMsg : '';
        t3lib_div::sysLog(
            $this->getMessage().' ['.get_class($this) . $debugMsg.']', 
            'pt_extbase', 
            3 // "error"
        );
        
        // write to TS log if appropriate
        if ($GLOBALS['TT'] instanceof t3lib_timeTrack) {
            $GLOBALS['TT']->setTSlogMessage($this->getMessage() . '['.get_class($this).': '.$this->debugMsg.']', 3);
        }
        
    }
    
    
    
    /**
     * Return the error type of the exception
     *
     * @param   void
     * @return  string
     */
    public function getErrType() {
        
        return $this->errType;
        
    }
    
    
    
    /**
     * Return the debug message of the exception
     *
     * @param   void
     * @return  string
     */
    public function getDebugMsg() {
        
        return $this->debugMsg;
        
    }
    
    
    /**
     * Returns if this exception is permament
     *
     * @param   void
     * @return  bool
     */
    public function isPermanent() {
        
        return $this->permanent;
        
    }
    
    
    
    /**
     * Set the permament status
     *
     * @param   bool    permanent status
     * @return  void
     */
    public function setPermanent($permament = true) {
        
        $this->permanent = $permament;
        
    }
    
}

?>