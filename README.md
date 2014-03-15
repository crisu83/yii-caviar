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

Add the a command to your console application configuration:

```
return array(
  ...
  'commandMap' => array(
    'generate' => array(
      'class' => '\crisu83\yii_caviar\Command',
      'basePath' => '<path-to-project-root>',
    ),
    ...
  ),
);
```

When that is done you can use it to generate code:

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
