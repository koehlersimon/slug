<?php

/***************************************************************
 * Config file for ext: "slug" by Simon Köhler
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Slug',
    'description' => 'Helps you managing the URL slugs of your TYPO3 site.',
    'category' => 'plugin',
    'author' => 'Simon Köhler',
    'author_email' => 'info@simon-koehler.com',
    'state' => 'beta',
    'uploadfolder' => 0,
    'clearCacheOnLoad' => 0,
    'version' => '1.0.10',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];