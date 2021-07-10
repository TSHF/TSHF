<?php
namespace App\Http\Entities;

use Illuminate\Support\Facades\DB;

class AvailabilityEntity
{
    protected $db;

    public function __construct()
    {
        $this->db = DB::connection('mysql');
    }

    public function getBookedSlots($pid, $date, $locationId)
    {
        $resp   = array();
        $select = DB::table('appointments')
            ->select("date", "start_time", "end_time", "location_id")
            ->where('profile_id', $pid)
            ->whereIn("date", $date);

        if ($locationId > 0) {
            $select->where('location_id', $locationId);
        }
        $result = $select->get()->toArray();
        if (!empty($result)) {
            $resp = $result;
        }
        return $resp;
    }
}
