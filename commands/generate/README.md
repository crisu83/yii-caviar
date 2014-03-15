yii-caviar
==========

Next generation code generation for Yii.

Purpose
-------

A new take on code generation for Yii that will change the way you develop.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist crisu83/yii-caviar "*"
```

or add

```
"crisu83/yii-caviar": "*"
```

to the require section of your `composer.json` file.

Usage
-----

```
yiic generate {generator} {app}:{name} [--key=value] ...
```

Generators
----------

The following generators will be included in the first release:

- component
- config
- controller
- layout
- model
- view
- webapp