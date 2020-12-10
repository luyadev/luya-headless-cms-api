<p align="center">
  <img src="https://raw.githubusercontent.com/luyadev/luya/master/docs/logo/luya-logo-0.2x.png" alt="LUYA Logo"/>
</p>

# LUYA HEADLESS CMS BRIDGE

[![LUYA](https://img.shields.io/badge/Powered%20by-LUYA-brightgreen.svg)](https://luya.io)

This module provides and out of the box ready API in order to consume the CMS informations from a PUBLIC, CORS-ready, Read-Only API.

## Installation

Install the extension through composer:

```sh
composer require luyadev/luya-headless-cms
```

Add the module to the config

```php
'modules' => [
    'api' => [
        'class' => 'luya\headless\cms\Module',
    ]
]
```

## APIs 

> The  module name is equal to the rest api prefix. When you register the module as `foobar` in the config the api would be `/foobar/menu?langId=x`.

+ Returns all contains indexed by its alias with all pages as a tree: `api/menu?langId=1`
+ Returns the placeholders with all blocks for a certain page: `api/page?id=8`
