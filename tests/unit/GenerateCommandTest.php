<?php

class GenerateCommandTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    public function testHelp()
    {
        $I = $this->codeGuy;

        $I->runCommand('generate --help');
        $I->runCommand('generate -h');
    }
}