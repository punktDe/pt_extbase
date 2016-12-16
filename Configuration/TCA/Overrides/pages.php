<?php
defined('TYPO3_MODE') or die();

$pageSortingColumn = [
    'sorting' => [
        'config' => [
            'type' => 'passthrough',
        ]
    ]
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $pageSortingColumn);
