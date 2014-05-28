<?php

class LayoutGeneratorTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    public function testRun()
    {
        $I = $this->codeGuy;

        $I->runCommand('generate layout --help');
        $I->runCommand('generate layout -h');
        $I->runCommand('generate layout foo');

        $I->canSeeFile('_data/app/views/layouts/foo.php');

        $I->removeDir('_data/app');
    }
}