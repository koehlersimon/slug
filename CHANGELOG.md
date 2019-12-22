# Changelog
All notable changes to this project will be documented in this file.

## [3.0.3] - 2019-12-21
- Compatibility set to 9.5 to 10.2 again!
- Status set to stable
- Several code optimizations done
- Page list can now display more different page types

## [3.0.0] - 2019-12-20
- Composer settings changed
- Backend interface design optimized
- New Feature: The pages list now shows hidden and even deleted pages. Those can not be edited yet, because the TYPO3 internal classes for generating slugs cause problems when pages are not in any tree root.
- New Feature: You can define a custom label field for page records in the extension setup

## [2.0.2] - 2019-12-20
- New Icon for the backend module
- Compatibility checked with TYPO3 10.2.0

## [2.0.1] - 2019-07-27
- Added new option to custom records: By setting "pid=" only records from the related parent page will be shown
- BUGFIX: Fixed bug in TCA fields, thanks to @Teddytrombone on Github
- Changed namespace from "GOCHILLA" to "SIMONKOEHLER", so please empty autoload cache after update from version 2.0.0
- Several code clean ups
- Bugfix: Solved title problems with custom records. Issue #15 on GitHub, thanks @alex-boehm
- Changed footer information and template

## [2.0.0] - 2019-07-08
- BREAKING: News module removed!
- NEW: Add as many custom tables as you want simply by TypoScript!

## [1.0.15] - 2019-07-07
- Added functionality to use ANY custom table from TypoScript settings, but this is only a BETA version for now!
- Added sorting by timestamp (page field "tstamp") for page slugs

## [1.0.14] - 2019-07-01
- Bugfix-Release
- Checked compatibility with TYPO3 9.5.8!

## [1.0.13] - 2019-05-14
- Bugfix-Release
- Due to a change in the basic functionality in relation to TYPO3 Ajax Requests, the extension was not compatible with TYPO3 9.5.6. This has now been fixed!

## [1.0.12] - 2019-01-09
- Bugfix-Release
- Now creates only unique slugs!
- Issue 'Duplicate entries for news' solved (https://github.com/koehlersimon/slug/issues/2)
- Several small code improvements and changes

## [1.0.11] - 2019-01-07

### Changed
- Code optimizations in List.html and NewsList.html template files

### Removed
- Standard doc header

### Added
- Added experimental but working action for tree views!
- Record count badge in table header of list views
- Bulk saving and generating Page slugs finally works!
- Table row and buttons for bulk editing of Page slugs


## [1.0.10] - 2019-01-05

### Changed
- Removed some files that are no longer in use
- General Optimization of the slug saving functions

### Removed
- Removed Ajax Route 'getPageInfo' and Action in 'AjaxController.php' because it is no longer needed at the moment
- Removed function 'googlePreview' from 'AjaxController.php'
- Removed function 'getNewsRecordInfo' from 'AjaxController.php'

### Added
- Possibiliy to disable the 'additional record info' button in Pages list in global Extension configuration
- Dynamic select menu options including translation of all labels into German and Spanish
- More stable check for the News module. First checks if module is active, then if News table exists
- Comparison of original News slug and freshly generated News slug
- Notification if the freshly generated slug is still the same like before
- Switch statement in List.html template to show different partials for record previews in the Pages list
- Language labels for record previews in the Pages list
- Section 'Known Problems' to the README.md file
- Input fields for news record slugs are now also highlighted when changed, analogous to the page records
- News icon for the button in the top right corner


## [1.0.9] - 2019-01-04

### Added
- Small bugfixes
- Basic documentation skeleton added, will be continued soon


## [1.0.8] - 2019-01-03

### Changed
- Changed extension settings type for 'newsMaxEntries' from type string to options
- Changed extension settings type for 'newsOrderBy' from type string to options
- Changed sorting of extension settings for 'News' tab
- Extended dropdown menu 'Entries per page' according to the values of the extension settings option select
- Replaced standard HTML selects of the filter form with Fluid ViewHelpers of type 'f:form.select'
- Several clean-ups of all PHP classes
- Cleaned up JavaScript codes, removed unnecessary lines of code
- Removed way too strict warnings when saving slugs
- Corrected validation and fixed some bugs in validation

### Added
- Dynamic page URL in Pages list added. When user changes the text of the slug input, the URL will change in realtime, too
- Bulk saving and generating functionality added to News view! (this will follow soon for pages, too)
- Added translations to buttons in Partials/GlobalHeader.html
- Added labels for select menus in filter, but still not implemented in the frontend
- Added sorting property 'is_siteroot' to the filter form for pages
- Added full page URL when page rootpage has a configured site, if not just shows the slug below the title


## [1.0.7] - 2019-01-01

### Changed
- Initial release of the beta version after some testing

### Added
- Created changelog file and let's go on from here, happy new year!
