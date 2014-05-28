<?php

class ActionGeneratorTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    public function testRun()
    {
        $I = $this->codeGuy;

        $I->runCommand('generate action --help');
        $I->runCommand('generate action -h');
        $I->runCommand('generate action foo');

        $I->canSeeFile('_data/app/actions/FooAction.php');

        $I->removeDir('_data/app');
    }
}