<?php
namespace App\Http\Entities;

use Illuminate\Support\Facades\DB;

class VendorEntity
{
    protected $db;

    public function __construct()
    {
        $this->db = DB::connection('mysql');
    }

    public function getVendorList(int $cityId, int $page, int $limit, array $param): array
    {
        $offset = 0;
        if (isset($page) && isset($limit) && $page != 0 && $limit != 0) {
            $offset = $page <= 1 ? 0 : $limit * ($page - 1);
        }

        $resp   = array();
        $select = DB::table('vendor')
            ->select("*")
            ->where('status', 1);

        if ($cityId > 0) {
            $select->where('city_id', $cityId);
        }
        if ($page != 0 && $limit != 0) {
            $select->offset($offset)
                ->limit($limit);
        }

        $result = $select->get()->toArray();

        if (!empty($result)) {
            $resp = $result;
        }
        return $resp;
    }

    public function getVendorListCount(int $cityId, array $param): int
    {

        $resp   = 0;
        $select = DB::table('vendor')
            ->select(DB::Raw("count(distinct(vendor_id)) as count"))
            ->where('status', 1);
        if ($cityId > 0) {
            $select->where('city_id', $cityId);
        };

        $result = $select->first();

        if (!empty($result)) {
            $resp = $result['count'];
        }
        return $resp;
    }

    public function getVendorDetail(int $vId): array
    {
        $resp   = array();
        $result = DB::table('vendor')
            ->where('vendor_id', $vId)
            ->where('status', 1)
            ->first();
        if (!empty($result)) {
            $resp = $result;
        }
        return $resp;
    }

    public function getVendorProducts(int $vendorId): array
    {
        $resp   = array();
        $select = DB::table('products as p')
            ->select("p.*", "img.name as image_name", "pv.variant_id", "pv.size", "pv.measure", "pv.price", "pv.per_order_limit")
            ->leftJoin('product_variant as pv', 'pv.product_id', '=', 'p.id')
            ->leftJoin('images as img', 'img.product_id', '=', 'p.id')
            ->where('p.vendor_id', $vendorId)
            ->where('pv.status', 1);

        $result = $select->get()->toArray();

        if (!empty($result)) {
            $resp = $result;
        }
        return $resp;
    }
}
