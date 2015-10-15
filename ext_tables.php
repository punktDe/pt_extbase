<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}



\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', '[pt_extbase] Tools for Extbase development');


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_ptextbase_tree_node');

// Custom CSS include
if (TYPO3_MODE=="BE") {
    $TBE_STYLES['inDocStyles_TBEstyle'] .= '@import "/typo3conf/ext/pt_extbase/Resources/Public/CSS/Backend.css";';
}
