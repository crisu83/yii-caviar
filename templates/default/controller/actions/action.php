<?php
/**
 * @var string $methodName
 * @var string $viewName
 */

return <<<EOD
/**
 * Displays the '$viewName' page.
 */
public function $methodName()
{
    \$this->render('$viewName');
}
EOD;
