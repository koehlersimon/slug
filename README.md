# ![](https://github.com/koehlersimon/slug/blob/master/ext_icon.gif?raw=true) slug - TYPO3 Extension

Official Repository of the 'slug' Backend Module for TYPO3 9.5, 10 and soon 11.1.0

The Slug backend module is designed to help manage large amounts of slugs for pages and extension records. Currently, it provides a simple list for pages and custom records, which can be filtered with different parameters. Slugs can be edited and saved quickly and efficiently. The modules regenerate or save **all slugs of the current list view** with just one click. I have tested the functionality with 500 empty news records so far, without any problem.

## VERSION 4.0.0 COMING SOON

If you like the slug extension, please consider to donate and help speed up the development.
Your ideas to improve and extend the slug module are more than welcome: [send a message](https//simon-koehler.com/en/contact)

[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://paypal.me/typo3freelancer/5)

## INTRODUCTION

Please use the latest version from the official TYPO3 repository (https://extensions.typo3.org/extension/slug/), if you want to make sure that nothing happens to your website. In any case I highly recommend a database backup if you want to work in a live website!!! If you use the current version from GitHub, you use it at your own risk!

## FEATURES

* NEW in v3.0.0: Show hidden and deleted records in the page list
* NEW in v3.0.0: Display up to 3000 records at once. But handle with care ;-)
* List only custom records from a specific page or folder, by using the "pid" parameter
* Add custom records of your extensions via TypoScript (see manual below)
* Quickly edit, save and regenerate slugs for pages and other record types (new in Version 2)
* Mass generation and storage of news slugs (up to 500 at once)
* List views filterable with different parameters
* Search engine entry Preview for pages, displays the updated slug as you type
* Uses TYPO3 core slug generation functions
* Extension configuration for default values like sorting, entries per page etc.

## CHANGELOG

See the changelog for more details:
https://github.com/koehlersimon/slug/blob/master/CHANGELOG.md

## USAGE

### Installation

* For Composer use ```composer require koehlersimon/slug```
* Download the latest version here: https://extensions.typo3.org/extension/slug/ or install it with the extension manager of your TYPO3 installation
* No further configuration is required, but you should delete all the backend caches after installation to make sure the extension is working properly.

## CUSTOM RECORDS

Note: To enable custom records functionality, you must activate the checkbox "Custom records enabled" in the extension settings panel!

Important to know: Editing the slugs works only if the desired table contains a field for the title and a field for the slug. The names of the fields can be determined by TypoScript. But be careful. If you use a wrong field, the slug extension can destroy your data. I take no responsibility for it. So it's best not to test in a live web site before.
Very important to know:
* If you want to use an image symbol, make sure the image exists. The slug extension is currently NOT checking this!
* You can only use tables that are correctly prepared for TYPO3 use
    * The configuration array **$GLOBALS['TCA']['tx_your_table_name']['columns']['your_slug_field']['config']** needs to exist in the TYPO3 system. Otherwise the system will throw errors.
    * The fields **crdate,tstamp,uid** AND your custom fields for the title and the slug need to exist in your table!

Here's the TypoScript code you will need to make a custom table work. Put it into the setup of your root page.
```typoscript
# Module configuration
module.tx_slug {
    settings{
        additionalTables{
            tx_news_domain_model_news{
                pid = 0
                label = News
                slugField = path_segment
                titleField = title
                icon = EXT:news/Resources/Public/Icons/news_domain_model_news.svg
            }
        }
    }
}
```

## KNOWN PROBLEMS

### Oops, an error occured!: Invalid configuration: "vendorName" is not set
This happens, when you try to use the extension with version 3.0.0 or higher, in a TYPO3 version 9.5 or lower version. Please make sure to use slug v2.0.2 when using TYPO3 version 9!

### Uncaught TYPO3 Exception: #1278450972: Class SIMONKOEHLER\Slug\Controller\PageController does not exist. Reflection failed.
This exception occurs after upgrading the extension from 2.0.0 to 2.0.xx because the most important namespaces in the extbase PHP code have changed. Therefore, it is essential to clear the system's main cache and autoload data.

### Slug generation for News records failed?

If a news record has no pid set in the database, the slug generation will fail. This may happen when you have imported news records from a third party extension or manually. Solution: Check, if all entries in the table 'tx_news_domain_model_news' have the field 'pid' set to a page or folder in the page tree.

### Error, when 'Unassigned site configurations' have been found

The error "Argument 2 passed to TYPO3\CMS\Core\Imaging\IconFactory::getIconForRecord() must be of the type array, null given..." can be a result of "Unassigned site configurations"

## Want to report an issue?

https://github.com/koehlersimon/slug/issues

## All other requests

**Contact:** https://simon-koehler.com/en/contact

## Do you want to learn TYPO3? Here's what you need:
Video Training TYPO3 9 LTS (German language)
https://www.digistore24.com/redir/246076/GOCHILLA/
