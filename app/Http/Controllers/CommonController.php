<?php
namespace App\Http\Controllers;

use App\Http\Models\CommonModel;
use Illuminate\Http\Request;

class CommonController
{
    private $commonLib;
    private $commonModel;
    public function __construct()
    {
        $this->commonLib   = new \App\Http\Library\Common();
        $this->commonModel = new \App\Http\Models\CommonModel();
    }

    public function getMasterList()
    {
        $resp = $this->commonLib->getMasterList();
        return $resp;
    }
    public function getCityList(Request $request)
    {
        $params             = array();
        $params['cityName'] = $request->get('cityName');

        $rule = array(
            'cityName' => 'required|string',
        );
        $response = $this->commonModel->validator($params, $rule);

        if (!empty($response)) {
            return $response;
        }
        $cityName = $params['cityName'];
        $limit    = 20;
        $resp     = $this->commonLib->getCityList($cityName, $limit);
        return $resp;
    }

}
