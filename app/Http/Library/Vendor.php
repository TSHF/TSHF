<?php
namespace App\Http\Library;

class Vendor
{
    private $vendorEntity;
    private $vendorModel;
    public function __construct()
    {
        $this->vendorEntity = new \App\Http\Entities\VendorEntity();
        $this->vendorModel  = new \App\Http\Models\VendorModel();
    }
    public function getVendorList($cityId, $page, $limit, $param)
    {
        $resp    = array();
        $vendors = $this->vendorModel->getVendorList($cityId, $page, $limit, $param);
        if (empty($vendors)) {
            throw new \App\Exceptions\ApiExceptions("No Venor found", 701);
        }
        $count = $this->vendorEntity->getVendorListCount($cityId, $param);

        $resp['data']['list']  = $vendors;
        $resp['data']['total'] = $count;

        return $resp;
    }

    public function getProductList($vendorId)
    {

        $detail = $this->vendorModel->getVendorDetail($vendorId);
        if (empty($detail)) {
            throw new \App\Exceptions\ApiExceptions("Vendor not found.", 901);
        }

        $products = $this->vendorModel->getVendorProducts($vendorId);
        if (empty($products)) {
            throw new \App\Exceptions\ApiExceptions("No products found.", 901);
        }

        $resp         = array();
        $resp['data']['vendorDetail'] = $detail;
        $resp['data']['products'] = $products;
        
        return $resp;
    }

}
