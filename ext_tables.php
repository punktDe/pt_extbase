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


// Custom CSS include
if (TYPO3_MODE=="BE")   {
	$TBE_STYLES['inDocStyles_TBEstyle'] .= '@import "/typo3conf/ext/pt_extbase/Resources/Public/CSS/Backend.css";';
}

$pageSortingColumn = array(
	'sorting' => array (
		'exclude' => 0,
		'label' => 'Sorting',
		'config' => array (
			'type' => 'input',
			'size' => 8,
			'eval' => 'int,required,trim',
			'readOnly' => TRUE
		)
	)
);

t3lib_div::loadTCA('pages');
t3lib_extMgm::addTCAcolumns('pages',$pageSortingColumn,1);
//t3lib_extMgm::addToAllTCAtypes('pages','sorting','', 'after:module');