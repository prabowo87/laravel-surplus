<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use DB;
class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
 
    	for($i = 1; $i <= 20; $i++){
    		DB::table('product_image')->insert([
    			'product_id' => $faker->numberBetween(1,20),
    			'image_id' => $faker->unique()->numberBetween(1,20),
    		]);
 
    	}
    }
}
