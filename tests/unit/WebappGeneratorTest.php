<?php

class WebappGeneratorTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    public function testRun()
    {
        $I = $this->codeGuy;

        $I->runCommand('generate webapp --help');
        $I->runCommand('generate webapp -h');
        $I->runCommand('generate webapp app');

        $I->canSeeFiles(
            array(
                '_data/app/components/Controller.php',
                '_data/app/components/UserIdentity.php',
                '_data/app/config/main.php',
                '_data/app/controllers/SiteController.php',
                '_data/app/runtime/.gitkeep',
                '_data/app/views/layouts/main.php',
                '_data/app/views/site/index.php',
                '_data/app/web/assets/.gitkeep',
            )
        );

        $I->removeDir('_data/app');
    }
}