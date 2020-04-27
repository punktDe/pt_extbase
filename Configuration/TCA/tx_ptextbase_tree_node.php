<?php
return [
    'ctrl' =>  [
        'title'             => 'TreeNode',
        'label'             => 'label',
        'tstamp'            => 'tstamp',
        'crdate'            => 'crdate',
        'origUid'           => 't3_origuid',
        'languageField'     => 'sys_language_uid',
        'transOrigPointerField'     => 'l18n_parent',
        'transOrigDiffSourceField'  => 'l18n_diffsource',
        'delete'            => 'deleted',
        'enablecolumns'     => [
            'disabled' => 'hidden'
        ],
        'iconfile'          => 'EXT:pt_extbase/Resources/Public/Icons/icon_tx_ptextbase_tree_node.png',
        'hideTable' => 1
    ],
    'interface' => [
        'showRecordFieldList'   => 'label',
    ],
    'types' => [
        '1' => ['showitem' => 'label'],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude'           => 1,
            'label'             => 'LLL:EXT:lang/locallang_general.php:LGL.language',
            'config'            => [
                'type'                  => 'select',
                'foreign_table'         => 'sys_language',
                'foreign_table_where'   => 'ORDER BY sys_language.title',
                'items' => [
                    ['LLL:EXT:lang/locallang_general.php:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/locallang_general.php:LGL.default_value', 0]
                ],
            ]
        ],
        'l18n_parent' => [
            'displayCond'   => 'FIELD:sys_language_uid:>:0',
            'exclude'       => 1,
            'label'         => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
            'config'        => [
                'type'          => 'select',
                'items'         => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_ptextbase_tree_node',
                'foreign_table_where' => 'AND tx_ptextbase_tree_node.uid=###REC_FIELD_l18n_parent### AND tx_ptextbase_tree_node.sys_language_uid IN (-1,0)',
            ]
        ],
        'l18n_diffsource' => [
            'config'        => [
                'type'      =>'passthrough',
            ]
        ],
        't3ver_label' => [
            'displayCond'   => 'FIELD:t3ver_label:REQ:true',
            'label'         => 'LLL:EXT:lang/locallang_general.php:LGL.versionLabel',
            'config'        => [
                'type'      =>'none',
                'cols'      => 27,
            ]
        ],
        'hidden' => [
            'exclude'   => 1,
            'label'     => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config'    => [
                'type'  => 'check',
            ]
        ],
        'label' => [
            'exclude'   => 0,
            'label'     => 'LLL:EXT:pt_extbase/Resources/Private/Language/locallang_db.xml:tx_ptextbase_tree_node.label',
            'config'    => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'namespace' => [
            'exclude'   => 0,
            'label'     => 'LLL:EXT:pt_extbase/Resources/Private/Language/locallang_db.xml:tx_ptextbase_tree_node.namespace',
            'config'    => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'lft' => [
            'exclude'   => 0,
            'label'     => 'left',
            'config'    => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ],
        ],
        'rgt' => [
            'exclude'   => 0,
            'label'     => 'right',
            'config'    => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ],
        ],
        'root' => [
            'exclude'   => 0,
            'label'     => 'right',
            'config'    => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ],
        ],
    ]
];
