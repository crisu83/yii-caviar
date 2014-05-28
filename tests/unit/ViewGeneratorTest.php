<?php

class ViewGeneratorTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    public function testRun()
    {
        $I = $this->codeGuy;

        $I->runCommand('generate view --help');
        $I->runCommand('generate view -h');
        $I->runCommand('generate view foo');

        $I->canSeeFile('_data/app/views/foo.php');

        $I->removeDir('_data/app');
    }
}