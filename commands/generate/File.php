<?php
/*
 * This file is part of yii-caviar.
 *
 * (c) 2014 Christoffer Niska
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace console\commands\generate;

use console\commands\generate\exceptions\Exception;

class File extends \CComponent
{
    /**
     * @var string an ID that uniquely identifies this code file.
     */
    public $id;

    /**
     * @var string the file path that the new code should be saved to.
     */
    public $path;

    /**
     * @var string the newly generated code content
     */
    public $content;

    /**
     * @var int
     */
    public $mode = 0666;

    /**
     * @var int
     */
    public $dirMode = 0777;

    /**
     * Constructor.
     *
     * @param string $path the file path that the new code should be saved to.
     * @param string $content the newly generated code content.
     */
    public function __construct($path, $content)
    {
        $this->path = strtr($path, array('/' => DIRECTORY_SEPARATOR, '\\' => DIRECTORY_SEPARATOR));
        $this->content = $content;
        $this->id = md5($this->path);
    }

    /**
     * @return boolean
     *
     * @throws Exception
     */
    public function save()
    {
        $dir = dirname($this->path);

        if (!is_dir($dir)) {
            $mask = @umask(0);
            $result = @mkdir($dir, $this->dirMode, true);
            @umask($mask);

            if (!$result) {
                throw new Exception("Unable to create the directory '$dir'.");
            }
        }

        if (@file_put_contents($this->path, $this->content) === false) {
            throw new Exception("Unable to write the file '{$this->path}'.");
        }

        $mask = @umask(0);
        @chmod($this->path, $this->mode);
        @umask($mask);

        return true;
    }

    /**
     * @return string
     */
    public function resolveExtension()
    {
        if (($pos = strrpos($this->path, '.')) !== false) {
            return substr($this->path, $pos + 1);
        }

        return '';
    }
}