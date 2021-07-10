<?php
namespace App\Http\Entities;

use Illuminate\Support\Facades\DB;

class OrderEntity
{
    protected $db;

    public function __construct()
    {
        $this->db = DB::connection('mysql');
    }
    function blockOrder($blockData){
        return $this->db->table('order_block')->insertGetId($blockData);
    }
}
