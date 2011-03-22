<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}


/**
 * Register LifeCycle Manager
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['hook_eofe'][] = 'EXT:pt_extbase/Classes/Lifecycle/HookManager.php:tx_PtExtbase_Lifecycle_HookManager->updateEnd';
?>