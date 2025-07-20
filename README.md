# WordPress hook

![GitHub Release](https://img.shields.io/github/v/release/dimitriBouteille/wp-module-hook) [![Packagist Downloads](https://img.shields.io/packagist/dt/dbout/wp-module-hook?color=yellow)](https://packagist.org/packages/dbout/wp-module-hook)

WordPress module developed for developers who want to quickly add WordPress hooks (action & filter) without having to manually add each hook in the `function.php` file (or elsewhere).

> [!TIP]
> To simplify the integration of this library, we recommend using WordPress with one of the following tools: [Bedrock](https://roots.io/bedrock/), [Themosis](https://framework.themosis.com/) or [Wordplate](https://github.com/wordplate/wordplate#readme).

## Documentation

This documentation only covers the specific points of this library, if you want to know more about WordPress `action` or `filter`, the easiest is to look at [the documentation of WordPress](https://developer.wordpress.org/plugins/hooks/).

You can find all the documentation in [the wiki](https://github.com/dimitriBouteille/wp-module-hook/wiki).

## Installation

**Requirements**

The server requirements are basically the same as for [WordPress](https://wordpress.org/about/requirements/) with the addition of a few ones :

- PHP >= 8.2
- [Composer](https://getcomposer.org/)

**Installation**

You can use [Composer](https://getcomposer.org/). Follow the [installation instructions](https://getcomposer.org/doc/00-intro.md) if you do not already have composer installed.

~~~bash
composer require dbout/wp-module-hook
~~~

In your PHP script, make sure you include the autoloader:

~~~php
require __DIR__ . '/vendor/autoload.php';
~~~


## Contributing

💕 🦄 We encourage you to contribute to this repository, so everyone can benefit from new features, bug fixes, and any other improvements. Have a look at our [contributing guidelines](CONTRIBUTING.md) to find out how to raise a pull request.

## Licence

Licensed under the MIT license, see [LICENSE](LICENSE).