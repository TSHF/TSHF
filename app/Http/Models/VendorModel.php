<?php
namespace App\Http\Models;

use \Illuminate\Support\Facades\Config;

class VendorModel
{
    private $vendorEntity;
    public function __construct()
    {
        $this->vendorEntity = new \App\Http\Entities\VendorEntity();
    }

    public function getVendorList($cityId, $page, $limit, $param)
    {
        $resp   = array();
        $result = $this->vendorEntity->getVendorList($cityId, $page, $limit, $param);

        if (!empty($result)) {
            foreach ($result as $val) {
                $data = array();

                $data['sellerId'] = $val['vendor_id'];
                $data['type']     = $val['type'];
                $data['name']     = $val['name'];
                $data['rating']   = $val['rating'];
                // $data['mobile']      = $val['mobile'];
                // $data['email']       = $val['email'];
                $data['address']     = $val['address'];
                $data['pincode']     = $val['pincode'];
                $data['description'] = $val['description'];
                $data['online']      = $val['online'];
                $data['imageUrl']    = config("constants.IMAGE_URL") . $val['type'] . "/" . $val['vendor_id'] . "/" . $val['image_name'];

                $resp[$val['vendor_id']] = $data;
            }
        }

        return $resp;
    }

    public function getVendorProducts($vendorId)
    {

        $resp   = array();
        $result = $this->vendorEntity->getVendorProducts($vendorId);

        if (!empty($result)) {
            foreach ($result as $val) {
                if (!isset($resp[$val['id']])) {
                    $data                = array();
                    $data['productId']   = $val['id'];
                    $data['type']        = $val['type'];
                    $data['name']        = $val['name'];
                    $data['description'] = $val['description'];
                    $data['veg']         = $val['veg'];
                    $data['status']      = $val['status'];
                    $data['imageUrl']    = config("constants.IMAGE_URL") . $val['type'] . "/" . $val['vendor_id'] . "/" . $val['image_name'];
                    $data['variants'][]  = $this->getVariant($val);

                    $resp[$val['id']] = $data;

                } else {
                    $resp[$val['id']]['variants'][] = $this->getVariant($val);
                }
            }
        }

        return array_values($resp);
    }

    public function getVariant($val)
    {
        $variant                  = array();
        $variant["variantId"]     = $val['variant_id'];
        $variant['productId']     = $val['id'];
        $variant['vendorId']      = $val['vendor_id'];
        $variant["size"]          = $val['size'];
        $variant["measure"]       = $val['measure'];
        $variant["price"]         = $val['price'];
        $variant["orderMaxLimit"] = $val['per_order_limit'];

        $variant["bookingKey"] = base64_encode(json_encode($variant));

        unset($variant['variantId']);
        unset($variant['productId']);
        unset($variant['vendorId']);

        return $variant;
    }

    public function getVendorDetail($vendorId)
    {
        $resp   = array();
        $result = $this->vendorEntity->getVendorDetail($vendorId);

        if (!empty($result)) {
            $resp['sellerId'] = $result['vendor_id'];
            $resp['type']     = $result['type'];
            $resp['name']     = $result['name'];
            $resp['rating']   = $result['rating'];
            // $data['mobile']      = $val['mobile'];
            // $data['email']       = $val['email'];
            $resp['address']     = $result['address'];
            $resp['description'] = $result['description'];
            $resp['online']      = $result['online'];
            $resp['pincode']     = $result['pincode'];
            $resp['currency']    = $result['currency'];
            $resp['imageUrl']    = config("constants.IMAGE_URL") . $result['type'] . "/" . $result['vendor_id'] . "/" . $result['image_name'];
        }

        return $resp;
    }

}
