<?php

class ControllerGeneratorTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    public function testRun()
    {
        $I = $this->codeGuy;

        $I->runCommand('generate controller --help');
        $I->runCommand('generate controller -h');
        $I->runCommand('generate controller foo');

        $I->canSeeFiles(
            array(
                '_data/app/controllers/FooController.php',
                '_data/app/views/foo/index.php',
            )
        );

        $I->removeDir('_data/app');
    }
}