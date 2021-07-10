<?php
namespace App\Http\Controllers;

use App\Http\Models\CommonModel;
use Illuminate\Http\Request;

class OrderController
{
    private $orderLib;
    private $userEntity;
    private $commonModel;
    public function __construct()
    {
        $this->orderLib    = new \App\Http\Library\Order();
        $this->userEntity  = new \App\Http\Entities\UserEntity();
        $this->commonModel = new \App\Http\Models\CommonModel();
    }
    public function blockOrder(Request $request)
    {
        $userDetail  = $request->get('userDetail');
        $orderDetail = $request->get('orderDetail');
        $locationId  = $request->get('locationId');
        $totalAmount = $request->get('totalAmount');
        $paymentType = $request->get('paymentType');

        if (empty($userDetail) || !isset($userDetail['user_id']) || $userDetail['user_id'] < 0) {
            throw new \App\Exceptions\ApiExceptions("User detail required.", 700);
        }
        if (empty($orderDetail)) {
            throw new \App\Exceptions\ApiExceptions("Order detail required.", 700);
        }
        if (!is_numeric($totalAmount) || $totalAmount <= 0) {
            throw new \App\Exceptions\ApiExceptions("Invalid totalAmount.", 700);
        }
        if (!is_numeric($locationId) || $locationId <= 0) {
            throw new \App\Exceptions\ApiExceptions("Valid user address location required.", 700);
        }
        $location = $this->userEntity->getUserLocation($userDetail['user_id'], $locationId);
        $orders   = array();
        foreach ($orderDetail as $key => $value) {
            $rule = array(
                'orderkey' => 'required|string',
                'quantity' => 'required|gt:0|max:5',
            );
            $response = $this->commonModel->validator($value, $rule);

            if (!empty($response)) {
                return $response;
            }
            $order             = array();
            $order['detail']   = json_decode(base64_decode($value['orderkey']), 1);
            $order['orderkey'] = $value['orderkey'];
            $order['quantity'] = $value['quantity'];

            $orders[] = $order;
        }
        return $this->orderLib->blockOrder($userDetail['user_id'], $orders, $locationId, $totalAmount, $paymentType);
    }
}
