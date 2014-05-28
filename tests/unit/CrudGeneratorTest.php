<?php

class CrudGeneratorTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    public function testRun()
    {
        $I = $this->codeGuy;

        $I->runCommand('generate crud --help');
        $I->runCommand('generate crud -h');
        $I->runCommand('generate crud actor');

        $I->canSeeFiles(
            array(
                '_data/app/models/Actor.php',
                '_data/app/controllers/ActorController.php',
            )
        );

        $I->removeDir('_data/app');
    }
}