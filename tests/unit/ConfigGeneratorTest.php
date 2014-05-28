<?php

class ConfigGeneratorTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    public function testRun()
    {
        $I = $this->codeGuy;

        $I->runCommand('generate config --help');
        $I->runCommand('generate config -h');
        $I->runCommand('generate config foo');

        $I->canSeeFile('_data/app/config/foo.php');

        $I->removeDir('_data/app');
    }
}