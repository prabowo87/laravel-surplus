<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use DB;
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        $categoryArray=['food','drink','snack','fast food','hot plate'];
    	for($i = 0; $i < 5; $i++){
    		DB::table('category')->insert([
    			'name' => $categoryArray[$i],
    			'enable' => true,
    		]);
 
    	}
    }
}
