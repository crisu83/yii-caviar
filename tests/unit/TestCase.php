<?php
/**
 * Created by PhpStorm.
 * User: Crisu
 * Date: 20.3.2014
 * Time: 22.55
 */

namespace crisu83\yii_caviar\tests\unit;

use crisu83\yii_testingtools\console\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function runCommand($cmd)
    {
        return $this->runConsoleCommand("php yiic.php generate $cmd", dirname(__DIR__));
    }

    protected function removeApp()
    {
        $this->removeDirectory(dirname(__DIR__) . '/_data/app');
    }

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
}