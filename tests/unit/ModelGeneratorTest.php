<?php

class ModelGeneratorTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    public function testRun()
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
}