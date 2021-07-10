<?php
namespace App\Http\Library;

class Availability
{
    private $searchEntity;
    private $availEntity;
    public function __construct()
    {
        $this->searchEntity = new \App\Http\Entities\SearchEntity();
        $this->availEntity  = new \App\Http\Entities\AvailabilityEntity();
    }

    public function getProfileAvailability($pid, $date, $locationId)
    {
        $detail = $this->searchEntity->getProfileDetail($pid);
        if (empty($detail)) {
            throw new \App\Exceptions\ApiExceptions("Profile not found.", 901);
        }

        $location = $this->searchEntity->getProfileLocation($pid, $locationId);
        if (empty($location)) {
            throw new \App\Exceptions\ApiExceptions("Profile location not found.", 901);
        }
        $profileIds = array($pid);
        if ($detail['type'] == 'artist') {
            $profileIds = array_column($location, 'parent_profile_id');
        }
        $timings = $this->searchEntity->getWorkingHours($profileIds);
        if (empty($timings)) {
            throw new \App\Exceptions\ApiExceptions("Profile service hour not found.", 901);
        }

        $timings = $this->prepareTimings($location, $timings);

        $slots = $this->availEntity->getBookedSlots($pid, $date, $locationId);

        $resp         = array();
        $resp['data'] = $this->prepareAvailableSlots($date, $timings, $slots, $detail['time_sloat']);

        return $resp;
    }

    public function prepareAvailableSlots($date, $timings, $bookedSlots, $slotSize)
    {
        $resp = array();
        foreach ($timings as $locId => $value) {
            foreach ($date as $dt) {
                $resp[$locId][$dt]['date']   = date("d M Y", strtotime($dt));
                $resp[$locId][$dt]['day']    = date("D", strtotime($dt));
                $resp[$locId][$dt]['status'] = 'closed';
                $resp[$locId][$dt]['slots']  = array();

                $timing = $value[$resp[$locId][$dt]['day']];
                if ($timing['status'] == 'open') {
                    $resp[$locId][$dt]['status'] = 'open';
                    $timeSlots                   = $this->prepareSlots($timing['openTime'], $timing['closeTime'], $slotSize);
                    $resp[$locId][$dt]['slots']  = $timeSlots;
                }
            }
        }

        foreach ($bookedSlots as $key => $val) {
            $slots = $this->prepareSlots($val['start_time'], $val['end_time'], $timings['slot_size']);
            if (isset($resp[$val['location_id']][$val['date']]) && !empty($slots)) {
                foreach ($slots as $key => $value) {
                    $resp[$val['location_id']][$val['date']]['slots'][$key]['status'] = 'booked';
                }
            }
        }
        return $resp;
    }

    public function prepareSlots($start_time, $end_time, $slot_size)
    {
        $timeSlots  = [];
        $start_time = strtotime($start_time);
        $end_time   = strtotime($end_time);
        $slot       = strtotime(date('H:i:s', $start_time) . ' +' . $slot_size . ' minutes');

        while ($slot <= $end_time) {

            $start                          = date('h:i A', $start_time);
            $end                            = date('h:i A', $slot);
            $timeSlots[$start . "-" . $end] = [
                'start'  => $start,
                'end'    => $end,
                'status' => 'available',
            ];

            $start_time = $slot;
            $slot       = strtotime(date('H:i:s', $start_time) . ' +' . $slot_size . ' minutes');
        }
        return $timeSlots;
    }

    public function prepareTimings($location, $timings)
    {
        $resp = array();
        foreach ($location as $key => $val) {
            if ($val['parent_profile_id'] > 0) {
                $timing                    = isset($timings[$val['parent_profile_id']]) ? $timings[$val['parent_profile_id']] : array();
                $resp[$val['location_id']] = $this->prepareWorkingHours($timing, $val['open_time'], $val['close_time']);
            }
        }
        return $resp;
    }
    public function prepareWorkingHours($timings, $start = 0, $end = 0)
    {
        $days = array('Mon' => 'Monday', 'Tue' => "Tuesday", 'Wed' => 'Wednesday', 'Thu' => "Thursday", 'Fri' => "Friday", 'Sat' => "Saturday", 'Sun' => "Sunday");

        $resp = array();
        foreach ($days as $key => $day) {
            $day            = $key;
            $key            = strtolower($key);
            $data           = array();
            $data['day']    = $day;
            $data['status'] = 'closed';
            if (isset($timings[$key]) && $start == 0 && $end == 0) {
                $data['status']    = 'open';
                $data['openTime']  = date('h:i A', strtotime($timings[$key]['open_time'] . ":00"));
                $data['closeTime'] = date('h:i A', strtotime($timings[$key]['close_time'] . ":00"));
            } else if (!isset($timings[$key]) && $start > 0 && $end > 0) {
                $data['status']    = 'open';
                $data['openTime']  = date('h:i A', strtotime($start . ":00"));
                $data['closeTime'] = date('h:i A', strtotime($end . ":00"));
            }
            $resp[$day] = $data;
        }

        return $resp;
    }
}
