<?php
namespace App\Http\Library;

class Order
{
    private $orderEntity;
    private $orderModel;
    public function __construct()
    {
        $this->orderEntity = new \App\Http\Entities\OrderEntity();
        $this->orderModel  = new \App\Http\Models\OrderModel();
    }
    public function blockOrder($userId, $orders, $locId, $totalAmt, $paymentType)
    {
       $bookingData=array();
       $blockData = array();
       
       $bookingData = $this->prepareBookingData($userId, $orders, $locId, $totalAmt, $paymentType);
       $blockData = $this->prepareBlockData($userId, $orders, $bookingData);

       $blockId= $this->orderModel->blockOrder($blockData);
       if($blockId > 0){
           return array("status"=>"success","blockId"=>$blockId);
       }else{
        return array("status"=>"error","message"=>"Order blocking failed");
       }

    }
    function prepareBookingData($userId, $orders, $locId, $totalAmt, $paymentType){
        $bookingData = array();
        $bookingData["user_id"] = $userId;
        $bookingData["order_id"] = 0;
        $bookingData["payment_status"] = "pending";
        $bookingData["payment_type"] = $paymentType;
        $bookingData["amount"] = $totalAmt;
        $bookingData["payment_ref"] = "";
        $bookingData["currency"] = "INR";
        $bookingData["status"] = "pending";
        $bookingData["order_date"] = date("Y-m-d H:i:s");
        $bookingData["expected_date"] = date("Y-m-d H:i:s",strtotime("+3days",strtotime($bookingData["order_date"])));
        $amt = 0;
        foreach($orders as $detail){
            $order =$detail["detail"];
            $bookingData["vender_id"] = $order["vendorId"];
            $product = array();
            $product["order_id"] = 0;
            $product["product_id"] = $order["productId"];
            $product["variant_id"] = $order["variantId"];
            $product["size"] = $order["size"];
            $product["measures"] = $order["measure"];
            $product["quantity"] = $detail["quantity"];
            $amt += $product["amount"] = $order["price"] * $detail["quantity"];
            $product["currency"] = "INR";
            $product["created_on"] = date("Y-m-d H:i:s");
            $bookingData["detail"][] = $product;
        }
        $bookingData["amount"] = $amt;
        return $bookingData;
    }
    function prepareBlockData($userId, $orders, $bookingData){
        $blockData = array();
        $blockData["user_id"] = $userId;
        $blockData["vendor_id"] = $bookingData["vender_id"];
        $blockData["status"] = "block";
        $blockData["block_data"] = json_encode($orders);
        $blockData["book_data"] = json_encode($bookingData);
        $blockData["created_on"] = date("Y-m-d H:i:s");

        return $blockData;
    }
}
