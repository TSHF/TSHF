<?php
namespace App\Http\Controllers;

use App\Http\Models\CommonModel;
use Illuminate\Http\Request;

class VendorController
{
    private $vendorLib;
    private $commonModel;
    public function __construct()
    {
        $this->vendorLib   = new \App\Http\Library\Vendor();
        $this->commonModel = new \App\Http\Models\CommonModel();
    }
    public function getVendorList(Request $request)
    {
        $params          = array();
        $params['page']  = $request->get('page');
        $params['limit'] = $request->get('limit');

        $rule = array(
            'page'  => 'nullable|gt:0',
            'limit' => 'nullable|gt:0|lte:50',
        );
        $response = $this->commonModel->validator($params, $rule);

        if (!empty($response)) {
            return $response;
        }
        $cityId = 0;
        $param  = array();
        $page   = $params['page'] ?? 1;
        $limit  = $params['limit'] ?? 10;

        return $this->vendorLib->getVendorList($cityId, $page, $limit, $param);
    }

    public function getProductList(Request $request)
    {
        $sellerId = $request->get('sellerId');
        if (empty($sellerId) || !is_numeric($sellerId) || $sellerId <= 0) {
            throw new \App\Exceptions\ApiExceptions("Invalid sellerId.", 700);
        }

        return $this->vendorLib->getProductList($sellerId);
    }
}
