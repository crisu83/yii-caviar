yii-caviar
==========

Next generation code generation for Yii.

Motivation
----------

You might be wondering why you should use Caviar instead of Gii so let us take a look at what Caviar does differently. The main disadvantage with Gii is that it is troublesome to write templates for it. Have you ever looked at its templates? If you have you know that they are a big mess. Compare the model generator [template](https://github.com/yiisoft/yii/blob/master/framework/gii/generators/model/templates/default/model.php) in Gii and the corresponding [template](https://github.com/Crisu83/yii-caviar/blob/master/templates/default/model/model.php) in Caviar. 

Caviar uses HEREDOC syntax in its templates which does not allow for any logical operations such as if clauses or loops within the template. Usually HEREDOC is not considered a good practice as it interrupts the code flow within your classes, but in this case its limitations actually works in our favor.

This means that you have to contain all your logic within the generator which is a good thing. Gii actually also does this to some degree when it outputs arrays for e.g. model rules. Caviar enforces the [PSR standards](https://github.com/php-fig/fig-standards) so there is no need for alternative code formatting.

Convinced? Follow the instructions below to install Caviar.

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
yiic generate {generator} {context}:{subject} [--key=value] ...
```

Where generator is the __name__ of the generator, __context__ is the name of your application (e.g. protected) and __subject__ is a name for what you are generating (e.g. foobar).

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

And at least the following are planned for the first release:

- action
- widget

You can also write your own generator by extending the ```Generator``` class.
