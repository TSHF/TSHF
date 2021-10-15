<?php
namespace App\Http\Entities;

use Illuminate\Support\Facades\DB;
use \Illuminate\Database\QueryException;

class UserEntity
{

    public function __construct()
    {
        $this->db = DB::connection('mysql');
    }

    public function getUserByToken($token)
    {
        return $this->db->table('token')
            ->select('user_id')
            ->where('token', '=', $token)
        // ->where('expired_on', '>=', date('Y-m-d H:i:s'))
            ->first();
    }

    public function getUserDetail($username, $password)
    {

        $result = $this->db->table('users')
            ->select('name')
            ->where('email', $username)
            ->where('password', md5($password))
            ->where('status', 1)
            ->first();

        return $result;

    }

    public function genrateToken($userId)
    {
        try {

            $token = $this->db->table('token')
                ->select('token', 'expired_on')
                ->where('user_id', $userId)
                ->first();

            if (!empty($token) && strtotime($token['expired_on']) > strtotime(date('Y-m-d H:i:s'))) {
                return $token['token'];
            }

            $this->db->table('token')
                ->where('user_id', $userId)
                ->delete();

            $createdOn = date('Y-m-d H:i:s');

            $insertToken               = array();
            $insertToken['user_id']    = $userId;
            $insertToken['token']      = md5($userId . rand(1111, 9999) . date('Y-m-d H:i:su'));
            $insertToken['expired_on'] = date('Y-m-d H:i:s', strtotime('+10 minutes', strtotime($createdOn)));
            $insertToken['created_on'] = $createdOn;

            $resp = $this->db->table('token')->insertGetId($insertToken);
            if ($resp) {
                return $insertToken['token'];
            } else {
                throw new \App\Exceptions\ApiExceptions("Something went wrong.", 600);
            }

        } catch (QueryException $exp) {
            report($exp);
            throw new \App\Exceptions\ApiExceptions($exp->getMessage(), 600);
        }

    }
    public function getCreateUser($userDetail){
        try{
            $user = $this->db->table('users')
                ->select('user_id')
                ->where(function ($query) use ($userDetail) {
                    $query->where('email', '=', $userDetail['email'])
                          ->orWhere('mobile', '=', $userDetail['mobile']);
                })->first();
                if(!empty($user)){
                    throw new \App\Exceptions\ApiExceptions('User already registered.', 201);
                }
                $userDetail['type']='user';
                $userDetail['username']=$userDetail['mobile'];
                $userDetail['password']=md5($userDetail['mobile']);
                $userDetail['is_verified']=0;
                $userDetail['created_on']=date('Y-m-d H:i:s');
                $resp = $this->db->table('users')->insertGetId($userDetail);
                if ($resp) {
                    return array('message'=>'User Created Successfuly.','userId'=>$resp,'token'=>$this->genrateToken($resp));
                } else {
                    throw new \App\Exceptions\ApiExceptions("Something went wrong.", 600);
                }
        } catch (QueryException $exp) {
            report($exp);
            throw new \App\Exceptions\ApiExceptions($exp->getMessage(), 600);
        }
    }

    public function validateUser($userId){
        return $this->db->table('users')
        ->select('user_id')
        ->where('user_id', $userId)
        ->where('status', 1)
        ->first();
    }

    public function addEditAddress($locations){
        try{
            foreach($locations as $loc){
                if($loc['location_id'] == 0){
                  $resp =  $this->db->table('location')->insertGetId($loc);
                }else{
                    $resp = $this->db->table('location')->where('location_id',$loc['location_id'])->update($loc);
                }
            }
            if ($resp) {
                return array('message'=>'Locations Created Successfuly.');
            } else {
                throw new \App\Exceptions\ApiExceptions("Something went wrong.", 600);
            }
        } catch (QueryException $exp) {
            report($exp);
            throw new \App\Exceptions\ApiExceptions($exp->getMessage(), 600);
        }
    }
}
