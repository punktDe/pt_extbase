<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');



t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', '[pt_extbase] Tools for Extbase development');


t3lib_extMgm::allowTableOnStandardPages('tx_ptextbase_tree_node');
$TCA['tx_ptextbase_tree_node'] = array (
    'ctrl' => array (
        'title'             => 'TreeNode',
        'label'             => 'label',
        'tstamp'            => 'tstamp',
        'crdate'            => 'crdate',
        'origUid'           => 't3_origuid',
        'languageField'     => 'sys_language_uid',
        'transOrigPointerField'     => 'l18n_parent',
        'transOrigDiffSourceField'  => 'l18n_diffsource',
        'delete'            => 'deleted',
        'enablecolumns'     => array(
            'disabled' => 'hidden'
        ),
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Node.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/icon_tx_ptextbase_tree_node.png'
    )
);


?>