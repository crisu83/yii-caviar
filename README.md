Caviar
======

Next generation code generation for Yii.

Motivation
----------

You might be wondering why you should use Caviar instead of Gii, so let us take a look at how they differ from each other.

The main disadvantage with Gii is that it is troublesome to write templates for it. Have you ever looked at one of its templates? If you have you know that they are quite hard to read. Compare the following [template in Gii](https://github.com/yiisoft/yii/blob/master/framework/gii/generators/model/templates/default/model.php) to the corresponding [template in Caviar](https://github.com/Crisu83/caviar/blob/master/templates/default/model/model.php). 

Caviar uses HEREDOC syntax for its templates, which does not allow for any logical operations, such as if-clauses or loops, within the actual template. Usually HEREDOC is not considered a good practice mainly because it interrupts the code flow within your classes, but in this case its limitations actually works in our favor.

The reason why Caviar uses HEREDOC syntax is that it uses logic-less templates for separation of concerns. This means that all logic must be placed in the generator and that only strings should be passed to the template. Instead of doing logical operations within the template we do them in the generator when we render content for the template. You can take a look at the model generator for an [example](https://github.com/Crisu83/caviar/blob/master/generators/ModelGenerator.php) on this.

Convinced? Follow the instructions below to install Caviar.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist crisu83/caviar "*"
```

or add

```
"crisu83/caviar": "*"
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
yiic generate <name> [<context>:]<subject> [--key=value] ...
```

Where __name__ is the name of the generator, __context__ is the name of your application (e.g. app) and __subject__ is a name for what you are generating.

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

And at least the following generators will be included in the first release:

- action
- widget
