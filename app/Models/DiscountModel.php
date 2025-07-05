<?php

namespace App\Models;

use CodeIgniter\Model;

class DiscountModel extends Model
{
    protected $table         = 'discount';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['tanggal', 'nominal', 'created_at','updated_at'];

}
