Caviar
======

Code generation with logic-less templates for Yii.

Caviar vs Gii
-------------

You might be wondering why you should use Caviar instead of Gii, so let us take a look at how they differ from each other.

The main disadvantage with Gii is that it is troublesome to write templates for it.
Have you ever looked at one of its templates? If you have you know that they are quite hard to read.
Compare the following [template in Gii](https://github.com/yiisoft/yii/blob/master/framework/gii/generators/model/templates/default/model.php) to the corresponding [template in Caviar](https://github.com/Crisu83/yii-caviar/blob/master/templates/default/model/model.txt).

Caviar uses plain text (.txt files) templates, which are compiled into php files to apply separation of concerns.
This means that all logic must be contained in the generator and that only strings can be passed to the template.
Instead of doing logical operations within the template we do them in the generator when we create the data for the template.
You can take a look at the model generator for an [example](https://github.com/Crisu83/yii-caviar/blob/master/generators/ModelGenerator.php) on this.

Convinced? Follow the instructions below to install Caviar.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
php composer.phar require --prefer-dist crisu83/yii-caviar "*"
```

or add

```bash
"crisu83/yii-caviar": "*"
```

to the require section of your `composer.json` file.

Usage
-----

Add the a command to your console application configuration:

```php
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

```bash
yiic generate generator [context:]subject [options]
```

Where __generator__ is the name of the generator, __context__ is the name of your application (e.g. app) and __subject__ is a name for the item that will be generated.

You can view the command help by running the following command:

```bash
yiic generate help
```

Or the help for a particular generator by appending ```-t``` (or ```--help```) to your command:

```bash
yiic generate component --help
```

Disable namespaces
------------------

Caviar uses namespaces by default, mainly because it has a proven to be a good practice in large scale applications, but it also allows you to easily disable namespaces for all generators.

Disabling namespaces for all generators can easily be done by adding the following to your configuration:

```php
'generate' => array(
  ...
  'defaultTemplate' => 'noNamespaces',
  'enableNamespaces' => false,
  'templates' => array(
    'noNamespaces' => '<path-to-caviar>/templates/no-namespaces',
  ),
)
```

Fancy UI
--------

Caviar has a fancy UI, here are a few screenshots showing the UI.

- https://www.dropbox.com/s/x29drr6ldkku7ft/Screenshot%202014-03-20%2017.32.20.png
- https://www.dropbox.com/s/vrr0vj709xtxba4/Screenshot%202014-03-20%2017.36.31.png
- https://www.dropbox.com/s/0mm2x6g1ad7d5fz/Screenshot%202014-03-20%2017.39.26.png

Generators
----------

The following generators are already supported:

- component
- config
- controller
- layout
- model
- view
- webapp

The following generators are planned to be included in the first release:

- action
- crud
- widget
