<?php

namespace App\Http\Models;

use App\Http\Entities\CommonEntity;
use Validator;

/**
 * Description of common
 *
 * @author mohit yagyasaini
 */
class CommonModel
{

    protected $commonEntity;

    public function __construct()
    {
        $this->commonEntity = new CommonEntity;
    }

    public function validator(array $data, array $rules, array $customMessage = array(), bool $singleError = true): array
    {
        $return       = array();
        $setValidator = Validator::make($data, $rules, $customMessage);
        if ($setValidator->fails()) {
            if ($singleError) {
//return single error
                throw new \App\Exceptions\ApiExceptions($setValidator->errors()->first(), 700);
            } else {
                $return = $setValidator->errors()->toArray();
            }
        }
        return $return;
    }

    /**
     * Change unformated date to readable date format for UI
     */
    public function DateFormatByTimezone(string $datetime, string $timezone, string $format = "Y-m-d H:i:s (P)", string $default_timezone = "Asia/Calcutta")
    {
        try {

            if (!empty($datetime) and !empty($timezone)) {
                $date = new \DateTime($datetime, new \DateTimeZone($default_timezone)); //"2016-03-05"
                $date->setTimezone(new \DateTimeZone($timezone)); //"America/Chicago"
                return $date->format($format);
            } else {
                return;
            }

        } catch (\Exception $e) {
            throw new \App\Exceptions\ApiExceptions("", 500);
        }
    }
    public function getServiceMaster()
    {
        $result = array();

        $resp = $this->commonEntity->getServiceMaster();
        if (!empty($resp)) {
            foreach ($resp as $val) {
                $data                = array();
                $data['serviceId']   = $val['service_id'];
                $data['serviceName'] = $val['service_name'];

                $result[] = $data;
            }
        }
        return $result;
    }
    public function getCategoryMaster()
    {
        $result = array();

        $list = $this->commonEntity->getCategoryMaster();
        if (!empty($list)) {
            foreach ($list as $val) {
                $data                     = array();
                $data['categoryMasterId'] = $val['category_master_id'];
                $data['categoryName']     = $val['category_name'];
                $data['type']             = $val['type'];

                $result[] = $data;
            }
        }

        return $result;
    }
    public function getCityList($cityName, $limit)
    {
        $result = array();

        $list = $this->commonEntity->getCityList($cityName, $limit);

        if (!empty($list)) {
            foreach ($list as $val) {
                $data              = array();
                $data['cityId']    = $val['city_id'];
                $data['city']      = $val['city'];
                $data['stateId']   = $val['state_id'];
                $data['state']     = $val['state'];
                $data['countryId'] = $val['country_id'];
                $data['country']   = $val['country'];
                $data['display']   = $val['city'].", ";
                if(!empty($val['state'])){
                    $data['display'] .= $val['state'].", ";
                }
                 $data['display']  .= $val['country'];
                $result[] = $data;
            }
        }

        return $result;

    }
}
