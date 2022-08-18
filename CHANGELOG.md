# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.4] - 2022-08-18
### Changed
* Fix updater using release assets
* Fix unaccessible callback method

## [2.0.3] - 2022-08-18
### Changed
* Replaced node-sass with (dart-)sass
* Fix PHP 8 deprecation notice
* Fix `rsync` issue when building packaged release
* Update Plugin Update Checker to 4.13

## [2.0.2] - 2022-08-02
### Changed
* Fix readme.md file
* Fix build process
* Fix typos

## [2.0.1] - 2022-07-28
### Changed
- Added missing updater

## [2.0.0] - 2022-07-28
### Changed
- Rewrite of most components
- Start using Composer to load dependencies
- Start using Webpack to create static assets
- Start using PSR-12 as Coding Style Standard
- Fix typos and better error messages

### Removed
- Plugin constants; these are now resolvable through the container instance
- Global function 'get_open_govpub_nav_tab'
- Global function 'the_open_govpub_nav_tab'
- Global function 'get_open_govpub_source_organizations'
- Global function 'get_open_govpub_setting'
- Global function 'get_open_govpub_scheduled_time'
- Global function 'get_open_govpub_last_import_string'
- Global function 'get_open_govpub_current_import_string'
- Global function 'get_open_govpub_service_config'
- Global function 'get_open_govpub_option'
- Global function 'get_open_govpub_types_api_args'
- Global function 'get_open_govpub_search_api_args'
- Global function 'update_open_govpub_sub_option'
- Global function 'update_open_govpub_option'
- Global function 'is_govpub_import_locked'
- Global function 'is_govpub_import_check_locked'
- Global function 'delete_open_govpub_option'


## 1.0.2: 2020-09-01
### Changed
- Removal of esc_url from the curl request

## 1.0.1: 2019-10-29
### Added
- Update class

### Changed
- Grammar error

## 1.0.0: 2019-10-01
- Initial release