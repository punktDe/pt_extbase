<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

// Define state cache, if not already defined
if (!is_array($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['tx_ptextbase'])) {
	$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['tx_ptextbase'] = array(
		'frontend' => 't3lib_cache_frontend_VariableFrontend',
		'backend' => 't3lib_cache_backend_DbBackend',
		'options' => array(
			'cacheTable' => 'tx_ptextbase_cache_state',
			'tagsTable'  => 'tx_ptextbase_cache_state_tags',
		)
	);
}

/**
 * Register LifeCycle Manager
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['hook_eofe'][] = 'EXT:pt_extbase/Classes/Lifecycle/HookManager.php:tx_PtExtbase_Lifecycle_HookManager->updateEnd';

/**
 * Include the eId dispatcher
 */
$TYPO3_CONF_VARS['FE']['eID_include']['ptxAjax'] = t3lib_extMgm::extPath('pt_extbase').'Classes/Utility/eIDDispatcher.php';
?>