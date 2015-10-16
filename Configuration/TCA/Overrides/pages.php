<?php
defined('TYPO3_MODE') or die();

$pageSortingColumn = array(
    'sorting' => array(
        'config' => array(
            'type' => 'passthrough',
        )
    )
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $pageSortingColumn);
