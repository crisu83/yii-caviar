<?php
/**
 * @var string $namespace
 * @var string $className
 * @var string $baseClass
 */
return <<<EOD
<?php

namespace $namespace;

class $className extends $baseClass
{
    public function actionIndex()
    {
        \$this->render('index');
    }
}
EOD;

