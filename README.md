# Module GTStudio Query Log

    gtstudio/module-querylog

- [Main Functionalities](#markdown-header-main-functionalities)
- [Installation](#markdown-header-installation)
- [Specifications](#markdown-header-specifications)
- [Usage](#markdown-header-usage)


## Main Functionalities
This module helps you to debug and find database query bottlenecks.
You will be able to see the query execution time, stack trace through the front end of the store in real time.

![](gabrielgts/magento2-query-log/raw/main/docs/images/module.gif)

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

- Unzip the zip file in `app/code/Gtstudio`
- Enable the module by running `php bin/magento module:enable Gtstudio_QuerieLog`
- Apply database updates by running `php bin/magento setup:upgrade`\*
- Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

- Install the module composer by running `composer require gtstudio/module-querylog`
- enable the module by running `php bin/magento module:enable Gtstudio_QuerieLog`
- apply database updates by running `php bin/magento setup:upgrade`\*
- Flush the cache by running `php bin/magento cache:flush`


## Specifications

- Console Command
   - dev:query-view:enable

- Console Command
   - dev:query-view:disable

## Usage

To enable you just need to run the following command :

`bin/magento dev:query-view:enable`.


To disable you must run the following command :

`bin/magento dev:query-view:disable`.

