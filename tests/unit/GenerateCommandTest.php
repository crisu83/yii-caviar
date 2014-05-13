<?php

namespace crisu83\yii_caviar\tests\unit;

use \Codeception\TestCase\Test;

class GenerateCommandTest extends Test
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

    public function testGenerateComponent()
    {
        $I = $this->codeGuy;

        $I->runCommand('generate component --help');
        $I->runCommand('generate component -h');
        $I->runCommand('generate component foo');

        $I->canSeeFile('_data/app/components/Foo.php');

        $I->removeDir('_data/app');
    }

    public function testGenerateConfig()
    {
        $I = $this->codeGuy;

        $I->runCommand('generate config --help');
        $I->runCommand('generate config -h');
        $I->runCommand('generate config foo');

        $I->canSeeFile('_data/app/config/foo.php');

        $I->removeDir('_data/app');
    }

    public function testGenerateController()
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

    public function testGenerateLayout()
    {
        $I = $this->codeGuy;

        $I->runCommand('generate layout --help');
        $I->runCommand('generate layout -h');
        $I->runCommand('generate layout foo');

        $I->canSeeFile('_data/app/views/layouts/foo.php');

        $I->removeDir('_data/app');
    }

    public function testGenerateModel()
    {
        $tables = array(
            'actor',
            'actor_info',
            'address',
            'category',
            'city',
            'country',
            'customer',
            'customer_list',
            'film',
            'film_actor',
            'film_category',
            'film_list',
            'film_text',
            'inventory',
            'language',
            'nicer_but_slower_film_list',
            'payment',
            'rental',
            'sales_by_film_category',
            'sales_by_store',
            'staff',
            'staff_list',
            'store',
        );

        $I = $this->codeGuy;

        $I->runCommand("generate model --help");
        $I->runCommand("generate model -h");

        foreach ($tables as $table) {
            $I->runCommand("generate model $table");
        }

        $I->canSeeFiles(
            array(
                '_data/app/models/Actor.php',
                '_data/app/models/ActorInfo.php',
                '_data/app/models/Address.php',
                '_data/app/models/Category.php',
                '_data/app/models/City.php',
                '_data/app/models/Country.php',
                '_data/app/models/Customer.php',
                '_data/app/models/CustomerList.php',
                '_data/app/models/Film.php',
                '_data/app/models/FilmActor.php',
                '_data/app/models/FilmCategory.php',
                '_data/app/models/FilmList.php',
                '_data/app/models/FilmText.php',
                '_data/app/models/Inventory.php',
                '_data/app/models/Language.php',
                '_data/app/models/NicerButSlowerFilmList.php',
                '_data/app/models/Payment.php',
                '_data/app/models/Rental.php',
                '_data/app/models/SalesByFilmCategory.php',
                '_data/app/models/SalesByStore.php',
                '_data/app/models/Staff.php',
                '_data/app/models/StaffList.php',
                '_data/app/models/Store.php',
            )
        );

        $I->removeDir('_data/app');
    }

    public function testGenerateWebApp()
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