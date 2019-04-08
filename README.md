# MAC address manipulation tool for PHP

[![Build Status](https://travis-ci.org/MatejKucera/mac-address.svg?branch=master)](https://travis-ci.org/MatejKucera/mac-address)
[![Coverage Status](https://coveralls.io/repos/github/MatejKucera/mac-address/badge.svg?branch=master)](https://coveralls.io/github/MatejKucera/mac-address?branch=master)
[![composer.lock available](https://poser.pugx.org/matejkucera/mac-address/composerlock)](https://packagist.org/packages/matejkucera/mac-address)
[![Latest Stable Version](https://poser.pugx.org/matejkucera/mac-address/v/stable)](https://packagist.org/packages/matejkucera/mac-address)

## Introduction
This library makes manipulation with MAC addresses easiser. It parses the input and 
allows you to print the MAC address in whatever format you want to, validate it's structure and recognize it's type.

## Installation

```
composer require matejkucera/mac-address
```

## Usage

### Basics
```php

use MatejKucera\MacAddress\Mac;

$mac = Mac::parse('00:1B:44:11:3A:B7');

# outputs 00-1B-44-11-3A-B7
echo $mac->get(Mac::FORMAT_DASH_BY_TWO);

# outputs 001B.4411.3AB7
echo $mac->get(Mac::FORMAT_DOT_BY_FOUR);

# All defined formats:
#  FORMAT_DOT_PER_TWO:     'xx.xx.xx.xx.xx.xx';
#  FORMAT_DOT_PER_FOUR:    'xxxx.xxxx.xxxx';
#  FORMAT_COLON_PER_TWO:   'xx:xx:xx:xx:xx:xx';
#  FORMAT_COLON_PER_FOUR:  'xxxx:xxxx:xxxx';
#  FORMAT_DASH_PER_TWO:    'xx-xx-xx-xx-xx-xx';
#  FORMAT_PLAIN:           'xxxxxxxxxxxx';

# You can defined your own format:
echo $mac->get('xxxx_xxxx_xxxx');

# In default it prints all characters (a-f) in uppercase.
# If you want lowercase, pass it as an argument.
echo $mac->get(FORMAT_PLAIN, Mac::CASE_LOWER);
```

### Global Settings

```php
# Define global behaviour:
Mac::setGlobalCase(Mac::CASE_LOWER);
Mac::setGlobalCase(Mac::FORMAT_DASH_PER_TWO);

# Not get() method uses global settings. It prints 00-1b-44-11-3a-b7.
echo $mac->get();
```

### Vendor & prefixes

This package is regularly updated (last digit in version changes) with SQLite database

```php
# Define global behaviour:
$mac = Mac::parse("78FE3D651485");

# Returns vendor object
$vendor = $mac->vendor();

# Now you can print vendor infos
echo $vendor->prefix;  // outputs: 78FE3D
echo $vendor->company; // outputs: Juniper Networks
echo $vendor->address; // outputs: 1133 Innovation Way Sunnyvale CA US 94089
```

### Planned features

This is included only as my personal TODO list :)
* better MAC validation
* strict/non-strict validator
* automatically updated vendors list
* online API alternative for vendors (instead of sqlite db)
