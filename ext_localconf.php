<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

/**
 * Register LifeCycle Manager
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['hook_eofe'][] = PunktDe\PtExtbase\Lifecycle\HookManager::class . '->updateEnd';

/**
 * Include the eId dispatcher in Frontend environment
 */
$TYPO3_CONF_VARS['FE']['eID_include']['ptxAjax'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('pt_extbase') . 'Classes/Utility/eIDDispatcher.php';

/**
 * Include the ajax dispatcher in Backend environment
 */
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
//    'ptxAjax', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('pt_extbase') . 'Classes/Utility/AjaxDispatcher.php:Tx_PtExtbase_Utility_AjaxDispatcher->initAndDispatch', FALSE
//);

// Scheduler Tasks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Tx_PtExtbase_Scheduler_SqlRunner_SqlRunnerTask'] = [
    'extension' => 'pt_extbase',
    'title' => 'SQL Runner',
    'description' => 'Runs an SQL file.',
    'additionalFields' => 'Tx_PtExtbase_Scheduler_SqlRunner_SqlRunnerTaskAdditionalFields'
];

/*
 * Test Scheduler Task
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][PunktDe\PtExtbase\Tests\Functional\Scheduler\TestTask::class] = [
    'extension' => 'pt_extbase',
    'title' => 'Pt_Extbase Test Abstract Scheduler Task',
    'description' => 'This Task is for Testing, do not run this task in Production Environment',
];
