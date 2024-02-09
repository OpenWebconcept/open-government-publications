# Plugin Open Government Publications

This WordPress plugin imports government publications from [officielebekendmakingen.nl](https://www.officielebekendmakingen.nl/) into WordPress and exposes them through the REST API.

## Overview

* [Usage](#usage)
    * [Search publications](#search-publications)
    * [Publication types](#publication-types)
* [Installation](#installation)
    - [For users](#for-users)
    - [For developers](#for-developers)
* [Changelog](#changelog)
* [Testing](#testing)
* [Security](#security)

## Usage

After installation of the plugin (see [Installation](#installation)), open the plugin settings page by navigating to "Open Publications" -> "Settings". An import process should automatically start after an organization on the settings page has been configured. If not, navigate to "Open Publications" -> "Import options" and start a manual import process.

When publications have been imported, they are automatically exposed through the WordPress REST API. The main endpoint can be found at: `https://yoursite.com/wp-json/owc/govpub/v1`.

### Search publications
Search through all publications. Optionally filter the results by using the parameters below.

**Endpoint:** `/wp-json/owc/openpub/v1/search`

**Parameters**:
All parameters are optional.
| Parameter | Type | Values | Effect | Default |
|--|--|--|--|--|
| s | `string` |  | The search query. | `""` (empty)
| limit | `int` |  | Limit the list of returned publications to the specified amount. | `20` |
| page | `int` |  | The page number for multipage results. | `1`
| order | `string` |  | The column to sort on. | `"date"`
| orderby | `string` | `"ASC"` or `"DESC"` | The sorting direction. | `"DESC"`
| open_govpub_type | `string` | | Find publications that are within this type. Expects the type' slug. | `""` (empty)

### Publication types
Publications are of a certain type. This endpoint exposes the list of available types. Uses `WP_Term` as storage.

**Endpoint:** `/wp-json/owc/openpub/v1/types`

**Parameters:**
All parameters are optional.
| Parameter | Type | Values | Effect | Default |
|--|--|--|--|--|
| return | `string` | `"object"`, `"array"`, `""` | Return all `WP_Term` data. Omitting this parameter returns a key=>value list of the slug and name. | `""` 
| hide_empty | `bool` | `0` or `1`| Either hide or show empty types. Pass a `1` to show empty types. | `1`

## Installation

### For users
1. Download the latest release from [the releases page](https://github.com/OpenWebconcept/open-government-publications/releases) 
2. Unzip and move all files to the `/wp-content/plugins/open-government-publications` directory.
3. Activate the plugin through the WordPress admin interface
4. Open the plugin settings page by navigating to "Open Publications" -> "Settings".

An import process should automatically start after an organization on the settings page has been configured. If not, navigate to "Open Publications" -> "Import options" and start a manual import process.

[Configuring an actual Cronjob](https://www.cloudways.com/blog/wordpress-cron-job/#how-to-set-up-a-real-cron-job) is highly recommended.

### For developers
To contribute to this project, you will need to download [Composer](https://getcomposer.org/) and [NPM](https://www.npmjs.com/).

1. Clone this repository to your machine and/or WordPress installation
2. Use Composer (`composer install`) and NPM (`npm i`) to install the required dependencies
3. Run `npm run watch` to automatically rebuild assest whenever a file changes or run `npm run build` for a one-off build. 

To create an optimized and zipped build, run the `package.sh` script. This also requires Composer and NPM to run.

Please adhere to the `PSR-12` coding standard when adding changes to this plugin. For more information, view the [PHP-FIG website](https://www.php-fig.org/psr/psr-12/).

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for more information on what has changed recently.

## Testing

Please make sure you have installed the dev dependencies and run:

``` bash
$ composer test
$ composer phplint
$ composer phpcompatibility
```

This project also uses Psalm for code analysis:
``` bash
$ composer psalm
```

## Security

If you discover any security related issues, please email s.dekroon@sudwestfryslan.nl instead of using the issue tracker.
