<?php

use App\Article;
use App\Facility;
use App\Interest;
use App\Service;
use App\Sub_interest;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('articles')->delete();

        $articles = array(
            ['name' => 'robe', 'nb_jaime' =>15,'nb_comment' =>20,  'city' => 'sousse','couleur'=>'rouge','categorie'=>'vetement mode femme','prix_vente'=>25,'prix_achat'=>50],
            ['name' => 'chaussure', 'nb_jaime' =>15,'nb_comment' =>20,  'city' => 'sousse','couleur'=>'bleu','categorie'=>'chaussure','prix_vente'=>30,'prix_achat'=>60],
            ['name' => 'sac', 'nb_jaime' =>15,'nb_comment' =>20,  'city' => 'sousse','couleur'=>'marron','categorie'=>'sac','prix_vente'=>15,'prix_achat'=>50]);


        // Loop through each user above and create the record for them in the database
        foreach ($articles as $a)
        {
            Article::create($a);
        }

        Model::reguard();
//
//        Model::unguard();
//
//        DB::table('interests')->delete();
//
//        $interests = array(
//            ['name' => 'interest1'],
//            ['name' => 'interest2'],
//            ['name' => 'interest3'],
//            ['name' => 'interest4'],
//        );
//
//        // Loop through each user above and create the record for them in the database
//        foreach ($interests as $interest)
//        {
//            Interest::create($interest);
//        }
//
//        Model::reguard();
//        Model::unguard();
//
//        DB::table('sub_interests')->delete();
//
//        $sub_interests = array(
//            ['name' => 'interest5','name_ar'=>'قص الشعر', 'interest_id'=>17],
//            ['name' => 'interest6','name_ar'=>'صبغ الشعر','interest_id'=>18],
//            ['name' => 'interest7','name_ar'=>'تلميع','interest_id'=>18],
//            ['name' => 'interest8','name_ar'=>'قص الشعر','interest_id'=>20],
//        );
//
//        // Loop through each user above and create the record for them in the database
//        foreach ($sub_interests as $sub_interest)
//        {
//            Sub_interest::create($sub_interest);
//        }
//
//        Model::reguard();
//        Model::unguard();
//
//       // DB::table('services')->delete();
//
//        $services = array(
//            ['sub_interest_id'=>6,'user_id'=>179,'price'=>1500,'type_price'=>'fixed'],
//            ['sub_interest_id'=>7,'user_id'=>182,'price'=>1400,'type_price'=>'fixed'],
//            ['sub_interest_id'=>8,'user_id'=>145,'price'=>800,'type_price'=>'Starting from']);
//
//
//        // Loop through each user above and create the record for them in the database
//        foreach ($services as $service)
//        {
//            Service::create($service);
//        }
//
//        Model::reguard();
//


    }

}
