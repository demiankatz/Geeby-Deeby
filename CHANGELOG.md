# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.1.0 - Currently in Development

### Added

- The check/fulltext command line tool now has an --updateRedirects option which
can be used to automatically update the database for URLs that respond with a
redirect status.
- Email is now more configurable than before (SMTP support, etc.).
- "Recently added" screens have been added for items, people, and series.
- Users with edit privileges can now toggle to edit mode using convenient links.

### Changed

- Email is now sent using the laminas-mail library.

### Removed

- Nothing.

### Fixed

- Nothing.

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
