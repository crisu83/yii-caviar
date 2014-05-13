<?php
/*
 * This file is part of Caviar.
 *
 * (c) 2014 Christoffer Niska
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace crisu83\yii_caviar\components;

class File extends \CComponent
{
    /**
     * @var string unique identifier for this file.
     */
    public $id;

    /**
     * @var string full path to where this file should be saved.
     */
    public $path;

    /**
     * @var string contents of this file.
     */
    public $content;

    /**
     * @var int file mode.
     */
    public $mode = 0666;

    /**
     * @var int directory mode.
     */
    public $dirMode = 0777;

    /**
     * Creates a new file.
     *
     * @param string $path full path to where this file should be saved.
     * @param string $content contents of this file.
     */
    public function __construct($path, $content)
    {
        $this->path = strtr($path, array('/' => DIRECTORY_SEPARATOR, '\\' => DIRECTORY_SEPARATOR));
        $this->content = $content;
        $this->id = md5($this->path);
    }

    /**
     * Saves this file.
     *
     * @return boolean whether the file was saved successfully.
     * @throws \crisu83\yii_caviar\Exception if the file or the directory cannot be created.
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
     * Returns the extension for this file.
     *
     * @return string extension.
     */
    public function resolveExtension()
    {
        if (($pos = strrpos($this->path, '.')) !== false) {
            return substr($this->path, $pos + 1);
        }

        return '';
    }
}