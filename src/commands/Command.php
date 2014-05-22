<?php
/*
 * This file is part of Caviar.
 *
 * (c) 2014 Christoffer Niska
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace crisu83\yii_caviar\commands;

use crisu83\yii_caviar\helpers\Line;

class Command extends \CConsoleCommand
{
    /**
     * @var string
     */
    protected $version = '1.0.0-beta';

    /**
     * @inheritDoc
     */
    public function usageError($message)
    {
        echo Line::begin('Error:', Line::RED)->nl();
        echo Line::begin()
            ->indent(2)
            ->text($message)
            ->nl();

        exit(1);
    }

    /**
     * Renders the a text containing the current version of Caviar.
     * @return string the rendered text.
     */
    protected function renderVersion()
    {
        return Line::begin('Caviar', Line::MAGENTA)
            ->text('version')
            ->text($this->version, Line::YELLOW)
            ->nl(2);
    }

    /**
     * Removes a specific directory.
     *
     * @param string $path full path to the directory to remove.
     */
    protected function removeDirectory($path)
    {
        if (!is_dir($path)) {
            return;
        }

        foreach (scandir($path) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $entryPath = $path . '/' . $entry;

            if (is_dir($entryPath)) {
                $this->removeDirectory($entryPath);
            } else {
                unlink($entryPath);
            }
        }

        rmdir($path);
    }

    /**
     * Normalizes the given file path by converting aliases to real paths.
     *
     * @param string $filePath file path.
     * @return string real path.
     */
    protected function normalizePath($filePath)
    {
        if (($path = \Yii::getPathOfAlias($filePath)) !== false) {
            $filePath = $path;
        }

        return $filePath;
    }
} 