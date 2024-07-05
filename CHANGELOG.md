# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.6.0 - 2024-07-05

### Added

- Ability to include additional details in search results.

### Changed

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.5.0 - 2024-03-21

### Added

- Nothing.

### Changed

- Raised minimum PHP required version to 8.1.
- Updated dependencies.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.4.1 - 2023-07-25

### Added

- Nothing.

### Changed

- Bootstrap was upgraded to release 3.4.1.
- jQuery was upgraded to release 3.7.0.

### Removed

- HTML5Shiv library (no longer needed).

### Fixed

- Improved PHP 8 compatibility in templates.

## 2.4.0 - 2023-03-24

### Added

- Improved navigation for tag editing.
- Visible links to external identifiers for authors, publishers and subjects/tags.

### Changed

- Raised minimum PHP version to 7.4.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.3.0 - 2022-06-14

### Added

- Unspecified creator list on "check" page.

### Changed

- Updated dependencies (Laminas, etc.).

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.2.0 - 2021-08-24

### Added

- \GeebyDeeby\Db\Table\PeopleURIs::getPeopleWithURIs() utility method.
- Last login tracking to assist with eventual removal of stale accounts.
- New tag_label route to redirect from a label to a tag ID.
- Spam account prevention through new "Join Reason" field.

### Changed

- Raised minimum PHP version to 7.3.

### Removed

- Nothing.

### Fixed

- Fixed bug #31: deal with session timeout more gracefully.
- Fixed bug #90: Translation links do not display if the target item is not part of a series.
- Fixed bug #93: alt titles from wrong items/series can be assigned to editions.
- Fixed bug accidentally introduced by #75: could not save new edition attributes

## 2.1.1 - 2021-01-29

### Added

- Nothing.

### Changed

- Nothing.

### Removed

- Nothing.

### Fixed

- Typos in title of "recent people" and "recent series" pages.

## 2.1.0 - 2021-01-08

### Added

- The check/fulltext command line tool now has an --updateRedirects option which
can be used to automatically update the database for URLs that respond with a
redirect status.
- Email is now more configurable than before (SMTP support, etc.).
- "Recently added" screens have been added for items, people, and series.
- Users with edit privileges can now toggle to edit mode using convenient links.
- User menus in the front end are now duplicated at the top and bottom of the display for convenience.

### Changed

- Clicking on a publisher name in the publisher tab of the series editor now links to the
publisher instead of popping up the advanced options dialog.
- Email is now sent using the laminas-mail library.
- If an item has both numbered and unnumbered entries in the same series, the unnumbered versions will not be displayed in the series summary list.

### Removed

- Nothing.

### Fixed

- The "items with reviews" display has been improved to fix some bugs.
- Links to parent items in the "known editions" list now link to the correct place.

## 2.0.0 - 2020-11-04

### Added

- Command line interface for running tools (accessible through cli.php)
- Command line utility for checking status of full text links.

### Changed

- Migrated dependencies from Zend Framework to Laminas.
- Minimum PHP version is now 7.1.3.
- Renamed GeebyDeeby\Db\PluginManagerFactory to GeebyDeeby\ServiceManager\AbstractPluginManagerFactory for greater reusability.

### Removed

- Nothing.

### Fixed

- Incorrect display of editions with no series set.

## 1.0.0 - 2020-09-23

Initial stable release.
