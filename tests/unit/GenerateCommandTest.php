<?php

namespace crisu83\yii_caviar\tests\unit;

class GenerateCommandTest extends TestCase
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    public function testHelp()
    {
        $this->assertEquals($this->runCommand('--help'), 0);
        $this->assertEquals($this->runCommand('-h'), 0);
    }

    public function testGenerateComponent()
    {
        $this->assertEquals($this->runCommand('component --help'), 0);
        $this->assertEquals($this->runCommand('component -h'), 0);
        $this->assertEquals($this->runCommand('component foo'), 0);

        $I = $this->codeGuy;
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/components/Foo.php');

        $this->removeApp();
    }

    public function testGenerateConfig()
    {
        $this->assertEquals($this->runCommand('config --help'), 0);
        $this->assertEquals($this->runCommand('config -h'), 0);
        $this->assertEquals($this->runCommand('config foo'), 0);

        $I = $this->codeGuy;
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/config/foo.php');

        $this->removeApp();
    }

    public function testGenerateController()
    {
        $this->assertEquals($this->runCommand('controller --help'), 0);
        $this->assertEquals($this->runCommand('controller -h'), 0);
        $this->assertEquals($this->runCommand('controller foo'), 0);

        $I = $this->codeGuy;
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/controllers/FooController.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/views/foo/index.php');

        $this->removeApp();
    }

    public function testGenerateLayout()
    {
        $this->assertEquals($this->runCommand('layout --help'), 0);
        $this->assertEquals($this->runCommand('layout -h'), 0);
        $this->assertEquals($this->runCommand('layout foo'), 0);

        $I = $this->codeGuy;
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/views/layouts/foo.php');

        $this->removeApp();
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

        $this->assertEquals($this->runCommand("model --help"), 0);
        $this->assertEquals($this->runCommand("model -h"), 0);

        foreach ($tables as $table) {
            $this->assertEquals($this->runCommand("model $table"), 0);
        }

        $I = $this->codeGuy;
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/Actor.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/ActorInfo.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/Address.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/Category.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/City.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/Country.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/Customer.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/CustomerList.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/Film.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/FilmActor.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/FilmCategory.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/FilmList.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/FilmText.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/Inventory.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/Language.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/NicerButSlowerFilmList.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/Payment.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/Rental.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/SalesByFilmCategory.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/SalesByStore.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/Staff.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/StaffList.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/models/Store.php');

        $this->removeApp();
    }

    public function testGenerateWebApp()
    {
        $this->assertEquals($this->runCommand('webapp --help'), 0);
        $this->assertEquals($this->runCommand('webapp -h'), 0);
        $this->assertEquals($this->runCommand('webapp app'), 0);

        $I = $this->codeGuy;
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/components/Controller.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/components/UserIdentity.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/config/main.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/controllers/SiteController.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/runtime/.gitkeep');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/views/layouts/main.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/views/site/index.php');
        $I->canSeeFile(dirname(__DIR__) . '/_data/app/web/assets/.gitkeep');

        $this->removeApp();
    }
}