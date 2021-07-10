<?php
namespace App\Http\Entities;

use Illuminate\Support\Facades\DB;

class CommonEntity
{
    protected $db;

    public function __construct()
    {
        $this->db = DB::connection('mysql');
    }
    public function getServiceMaster()
    {
        return $this->db->table('service_master')
            ->select('service_id', 'service_name')
            ->get()->toArray();
    }
    public function getCategoryMaster()
    {
        return $this->db->table('profile_category_master')
            ->select('category_master_id', 'category_name', 'type')
            ->get()->toArray();
    }
    public function getCityList($cityName, $limit)
    {
        return $this->db->table('site_location_city as slc')
            ->select('slc.city_id', 'slc.city', 'slc.state_id', 'slc.country_id', 'ls.state', 'lc.country')
            ->leftJoin('site_location_country as lc', 'lc.country_id', '=', 'slc.country_id')
            ->leftJoin('site_location_state as ls', 'ls.state_id', '=', 'slc.state_id')
            ->where('slc.city', 'like', $cityName . "%")
            ->limit($limit)
            ->get()->toArray();
    }
}
