<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
        $table->date('date');
        $table->integer('nb_jaime');
        $table->integer('nb_comment');
        $table->string('description');
        $table->string('brund');
        $table->string('image');
        $table->string('city');
            $table->string('couleur');
            $table->string('taille');
            $table->string('categorie');
        $table->float('prix_vente');
        $table->float('prix_achat') ;
        $table->integer('remise');
        $table->string('etat');
       // $table->integer('user_id');
        $table->timestamps();
            //$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
