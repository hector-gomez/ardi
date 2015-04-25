ardi
====
Ardi - a PHP framework for web designers

## User manual

The following sections try to provide high-level information on how to use the framework.
A skeleton project will be created in a separate repository to make it easier to get started.

### Configuration

Inside the _config_ folder the following files can be found:

* **app.ini** for general configuration: supported and default language
* **routes.ini** for the mapping of routes (in different languages) to views

You may place more configuration files in this folder and use `ConfigReader` to access their properties. The default
reader is for app.ini, if you want a reader for _my-config.ini_ then do:

```
$reader = ConfigReader::getReader('my-config');
```

Readers are cached and reused for greater performance.

### Translation

Inside the _lang_ folder you must create an ini file per supported language, with the locale as filename
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

### Views and layouts

A web page is the sum of a _view_ and a _layout_. The view is exclusive to that web page, and is embedded inside a
layout.

Layouts define the global skeleton of the web page, with elements such as `<html>` and `<head>` and even parts of the
`<body>`. The view goes inside the body tag, but can be only a part of it. A use case is having the layout include a
header or footer and the view only what the actual (distinct) contents of that particular page are.

Layouts are contained in the `layouts` folder, whereas views reside inside the `views` folder. View files are named
after the view name, which is specified in `config/routes.ini`. The layout used by a particular view can be decided
when this is instantiated but at the moment only `default.phtml` can be used. This will be improved in future releases.

Let's take the example of a contact page. The path will be `/en/contact` for English, `/es/contacto` for Spanish.

In `routes.ini` these two paths are added, pointing to the same view, _contactform_:

```
[en]
contact = contactform

[es]
contacto = contactform
```

Now, create `views/contactform.phtml` with the contents of the page (e.g. the contact form).

#### The site root

What about the root of the website, the entry point?

You can give the view for the root whatever name you like, and then define it in `routes.ini` the same way for all
languages, since it doesn't have a path that needs to be translated. Following the previous example, if you wish to
name it _home_, the routes would look like this:

```
[en]
root = home
contact = contactform

[es]
root = home
contacto = contactform
```

Now create `views/home.phtml` and it will be used for both `/en` and `/es`, the root paths for the site in each
language. You may, of course, choose to have a different view for a specific language.

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

The oldest supported PHP version is 5.3.2, but to run absolutely all unit tests PHP 5.4 and Xdebug are required. If you
do not have either of them some tests will be skipped.
