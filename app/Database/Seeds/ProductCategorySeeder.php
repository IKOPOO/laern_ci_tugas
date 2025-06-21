<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Smartphone',
                'slug' => 'smartphone',
                'description' => 'Kategori untuk ponsel pintar',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Laptop',
                'slug' => 'laptop',
                'description' => 'Kategori untuk perangkat laptop',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Televisi',
                'slug' => 'televisi',
                'description' => 'Kategori untuk TV LED dan Smart TV',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        $this->db->table('product_category')->insertBatch($data);
    }
}
