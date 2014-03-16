<?php
/*
 * This file is part of yii-caviar.
 *
 * (c) 2014 Christoffer Niska
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace crisu83\yii_caviar;

class Renderer extends \CComponent
{
    /**
     * @param string $fileName
     * @param array $_params_
     * @return string
     * @throws Exception
     */
    public function renderFile($fileName, array $_params_ = array())
    {
        if (!is_file($fileName)) {
            throw new Exception("The view file '$fileName' does not exist.");
        }

        if (is_array($_params_)) {
            extract($_params_, EXTR_PREFIX_SAME, 'params');
        } else {
            $params = $_params_;
        }

        return preg_replace('/(<?php)/', "$1\n" . $this->renderBanner(), require($fileName), 1);
    }

    /**
     * @return string
     */
    protected function renderBanner()
    {
        return <<<EOD
/**
 * This file was generated by Caviar.
 * http://github.com/Crisu83/yii-caviar
 */
EOD;
    }
} 