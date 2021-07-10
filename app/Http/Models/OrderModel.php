<?php
namespace App\Http\Models;

class OrderModel
{
    private $vendorEntity;
    public function __construct()
    {
        $this->orderEntity = new \App\Http\Entities\OrderEntity();
    }
       function blockOrder($blockData){
        return $this->orderEntity->blockOrder($blockData);
       }
}
