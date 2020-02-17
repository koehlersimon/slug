<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        if (TYPO3_MODE === 'BE') {

            /***************
             * Make the extension configuration accessible
             */
            $extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
            );
            $slugConfiguration = $extensionConfiguration->get('slug');

            /* Register main site module for slug editing
            *  NOTE: This function has to be replaced with the outcommented
            *  Function below!
            */
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'SIMONKOEHLER.Slug',
                'site',
                'slugs',
                '',
                [
                    'Page' => 'list,site,tree,ajaxList',
                    'Extension' => 'additionalTable',
                ],
                [
                    'access' => 'user,group',
                    'icon'   => 'EXT:slug/Resources/Public/Icons/slug-be-module.svg',
                    'labels' => 'LLL:EXT:slug/Resources/Private/Language/locallang_slugs.xlf',
                ]
            );

            /*
            * Prepared code for upcoming TYPO3 version 11!
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'Slug',
                'site',
                'slugs',
                '',
                [
                    \SIMONKOEHLER\Slug\Controller\PageController::class => 'list,site,tree',
                    \SIMONKOEHLER\Slug\Controller\ExtensionController::class => 'additionalTable'
                ],
                [
                    'access' => 'user,group',
                    'icon'   => 'EXT:slug/Resources/Public/Icons/slug-be-module.svg',
                    'labels' => 'LLL:EXT:slug/Resources/Private/Language/locallang_slugs.xlf',
                ]
            );
            */


        }

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('slug', 'Configuration/TypoScript', 'Slug');
    }
);
