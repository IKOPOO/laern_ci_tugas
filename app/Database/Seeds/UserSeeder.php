<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create('id_ID');

        //for ($i = 0; $i < 10; $i++) {
            $data = [
                'username' => 'turtle.owen',
                'email' => 'turlteowen@gmail.com',
                'password' => password_hash('123456789', PASSWORD_DEFAULT),
                'role' => 'admin',
                'created_at' => date("Y-m-d H:i:s"),
            ];
            //print_r($data);
            $this->db->table('user')->insert($data);
        //}
    }
}
