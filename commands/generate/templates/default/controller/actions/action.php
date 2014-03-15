<?php
/**
 * @var string $methodName
 * @var string $viewName
 */

return <<<EOD
    public function $methodName()
    {
        \$this->render('$viewName');
    }
EOD;
