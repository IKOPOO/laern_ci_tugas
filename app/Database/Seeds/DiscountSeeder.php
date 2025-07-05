<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DiscountSeeder extends Seeder
{
    public function run()
    {
        $discount = [
          ['tanggal' => date('Y-m-d'), 'nominal' => 200000],
          ['tanggal' => date('Y-m-d', strtotime('+1 day')), 'nominal' => 120000],
          ['tanggal' => date('Y-m-d', strtotime('+2 day')), 'nominal' => 120000],
          ['tanggal' => date('Y-m-d', strtotime('+3 day')), 'nominal' => 120000],
          ['tanggal' => date('Y-m-d', strtotime('+4 day')), 'nominal' => 120000],
          ['tanggal' => date('Y-m-d', strtotime('+5 day')), 'nominal' => 120000],
          ['tanggal' => date('Y-m-d', strtotime('+6 day')), 'nominal' => 390000],
          ['tanggal' => date('Y-m-d', strtotime('+7 day')), 'nominal' => 200000],
          ['tanggal' => date('Y-m-d', strtotime('+9 day')), 'nominal' => 250000],
          ['tanggal' => date('Y-m-d', strtotime('+11 day')), 'nominal' => 100000],
          ['tanggal' => date('Y-m-d', strtotime('+12 day')), 'nominal' => 150000],
          ['tanggal' => date('Y-m-d', strtotime('+14 day')), 'nominal' => 220000],

        ];

        foreach ($discount as $data) {
            $this->db->table('discount')-> insert([
              'tanggal'    => $data['tanggal'],
              'nominal'    => $data['nominal'],
              'created_at' => date('Y-m-d H:i:s'),
              'updated_at' => date('Y-m-d H:i:s'),
             ]);
        }
    }
}
