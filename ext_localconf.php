<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}


// TODO this is deactivated but we set it anyways in Tx_PtExtbase_State_Session_Storage_DBAdapterFactory since some websites crash otherwise. Fix this if you want to use caching!!!
/**
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
*/

/**
 * Register LifeCycle Manager
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['hook_eofe'][] = 'EXT:pt_extbase/Classes/Lifecycle/HookManager.php:tx_PtExtbase_Lifecycle_HookManager->updateEnd';

/**
 * Include the eId dispatcher in Frontend environment
 * TODO Mind, that there is no access controll ATM!!!!
 */
$TYPO3_CONF_VARS['FE']['eID_include']['ptxAjax'] = t3lib_extMgm::extPath('pt_extbase').'Classes/Utility/eIDDispatcher.php';

/**
 * Include the ajax dispatcher in Backend environment
 * TODO Mind, that there is no access controll ATM!!!
 */
$TYPO3_CONF_VARS['BE']['AJAX']['ptxAjax'] = t3lib_extMgm::extPath('pt_extbase').'Classes/Utility/AjaxDispatcher.php:Tx_PtExtbase_Utility_AjaxDispatcher->initAndDispatch';

// Scheduler Tasks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Tx_PtExtbase_Scheduler_SqlRunner_SqlRunnerTask'] = array(
    'extension' => $_EXTKEY,
    'title' => 'SQL Runner',
    'description' => 'Runs an SQL file.',
	'additionalFields' => 'Tx_PtExtbase_Scheduler_SqlRunner_SqlRunnerTaskAdditionalFields'
);

?>