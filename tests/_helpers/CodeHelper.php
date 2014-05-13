<?php
namespace Codeception\Module;

class CodeHelper extends \Codeception\Module
{
    public function runCommand($cmd)
    {
        $pipes = array();

        $process = proc_open(
            "php yiic.php $cmd",
            array(
                array('pipe', 'r'),
                array('pipe', 'w'),
                array('pipe', 'w'),
            ),
            $pipes,
            dirname(__DIR__)
        );

        stream_get_contents($pipes[1]);

        foreach (array_keys($pipes) as $descriptor) {
            fclose($pipes[$descriptor]);
        }

        $this->assertEquals(proc_close($process), 0);
    }

    public function seeFile($path)
    {
        $this->assertTrue(is_file(dirname(__DIR__) . '/' . $path));
    }

    public function seeFiles($paths)
    {
        foreach ($paths as $path) {
            $this->seeFile($path);
        }
    }

    public function seeDirectory($path)
    {
        $this->assertTrue(is_dir(dirname(__DIR__) . '/' . $path));
    }

    public function removeDir($path)
    {
        $this->removeDirectory(dirname(__DIR__) . '/' . $path);
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
