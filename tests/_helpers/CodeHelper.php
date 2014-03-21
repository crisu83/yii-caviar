<?php
namespace Codeception\Module;

class CodeHelper extends \Codeception\Module
{
    public function seeFile($path)
    {
        $this->assertTrue(is_file($path));
    }

    public function seeDirectory($path)
    {
        $this->assertTrue(is_dir($path));
    }
}
