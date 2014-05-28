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

        $I->runCommand('generate component --help');
        $I->runCommand('generate component -h');
        $I->runCommand('generate component foo');

        $I->canSeeFile('_data/app/components/Foo.php');

        $I->removeDir('_data/app');
    }
}