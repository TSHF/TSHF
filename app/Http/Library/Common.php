<?php
namespace App\Http\Library;

use App\Http\Models\CommonModel;

class Common
{

    private $commonModel;
    public function __construct()
    {
        $this->commonModel = new \App\Http\Models\CommonModel();
    }
    public function getMasterList()
    {
        $resp                       = array();
        $resp['data']['services']   = $this->commonModel->getServiceMaster();
        $resp['data']['categories'] = $this->commonModel->getCategoryMaster();

        return $resp;
    }
    public function getCityList($cityName, $limit)
    {
        $resp         = array();
        $resp['data'] = $this->commonModel->getCityList($cityName, $limit);
        return $resp;
    }
}
