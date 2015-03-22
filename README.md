ardi
====
Ardi - a PHP framework for web designers

## User manual

The following sections try to provide high-level information on how to use the framework.
A skeleton project will be created in a separate repository to make it easier to get started.

### Configuration

Inside the _config_ folder the following files can be found:

* **app.ini** for general configuration: supported and default language

You may place more configuration files in this folder and use `ConfigReader` to access their properties. The default
reader is for app.ini, if you want a reader for _my-config.ini_ then do:

```
$reader = ConfigReader::getReader('my-config');
```

Readers are cached and reused for greater performance.

### Translation

Inside the _lang_ folder you must create a ini file per supported language, with the locale as filename
(e.g. _en.ini_,_es.ini_, _fr.ini_, _de.ini_).

A translation file must contain a section per view and a _common_ section, with values any view may access. Example:

```
[common]
site_name = Ardi-powered website!

[home]
meta_description = Welcome to this awesome website
meta_keywords = ardi, php, framework

[contact]
meta_description = Get in touch with us
meta_keywords = contact, form, email, phone, fax
```

In the example, the value for _meta_description_ changes from one view to another, while the _site_name_ is the same.
Any view could provide a different value for _site_name_, hence overriding it.


## Contributing

First of all, thank you for considering to contribute to this project. Here are a few hints that will help you get
started.

### Install Composer

We use Composer to manage dependencies.

Follow the [installation instructions](https://phpunit.de/manual/current/en/installation.html#installation.composer)
to perform a local or global install.

Once Composer is ready run `php composer.phar install` and all dependencies will be downloaded. You are good to go!

### Write unit tests with PHPunit

Please make sure your code is covered by unit tests. A configuration file is provided to help you generate code coverage
of the right sources only. Remember that you need Xdebug installed.

Using the PHPunit installation downloaded with Composer and your default PHP interpreter:

```
vendor/phpunit/phpunit/phpunit --coverage-html tmp/test-report --configuration phpunit.xml tests/
```
