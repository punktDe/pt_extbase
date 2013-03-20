<?php
/***************************************************************
 * Copyright notice
 *
 *	2010 Daniel Lienert <daniel@lienert.cc>, Michael Knoll <mimi@kaktusteam.de>
 * All rights reserved
 *
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * This script loads the required environment to dispatch an extbase call
 *
 * Include this script in ext_localconf:
 * $TYPO3_CONF_VARS['FE']['eID_include']['ajaxDispatcher'] = t3lib_extMgm::extPath('pt_extbase').'Classes/Utility/eIDDispatcher.php'
 *
 *
 * @package Utility
 * @author Daniel Lienert <daniel@lienert.cc>
 */

require_once t3lib_extMgm::extPath('pt_extbase') . 'Classes/Utility/AjaxDispatcher.php';

//Connect to database
tslib_eidtools::connectDB();


// Init TSFE for database access
$GLOBALS['TSFE'] = t3lib_div::makeInstance('tslib_fe', $TYPO3_CONF_VARS, 0, 0, true);
$GLOBALS['TSFE']->sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
$GLOBALS['TSFE']->initFEuser();
$dispatcher = t3lib_div::makeInstance('Tx_PtExtbase_Utility_AjaxDispatcher'); /** @var $dispatcher Tx_PtExtbase_Utility_AjaxDispatcher */


// ATTENTION! Dispatcher first needs to be initialized here!!!
echo $dispatcher->initCallArguments()->dispatch();
?>
