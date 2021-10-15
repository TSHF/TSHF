<?php

namespace App\Http\Controllers;

use App\Http\Entities\UserEntity;
use App\Http\Models\commonmodel;
use Illuminate\Http\Request;

/**
 * Description of User
 *
 * @author mohit
 */
class UserController
{

    protected $commonModel;

    public function __construct(UserEntity $userentity)
    {
        $this->userEntity  = $userentity;
        $this->commonModel = new commonmodel();
    }

    public function auth(Request $request)
    {
        $params             = array();
        $params['username'] = $request->get('username');
        $params['password'] = $request->get('password');

        $message = "Login credentials are not correct";
        $rule    = array(
            'username' => 'required|string',
            'password' => 'required|string',
        );
        $response = $this->commonModel->validator($params, $rule);
        if (!empty($response)) {
            return $response;
        }

        $data = $this->userEntity->getUserDetail($params['username'], $params['password']);

        if (!empty($data)) {
            $response                  = array();
            $response['data']['name']  = $data['name'];
            $response['data']['token'] = $this->userEntity->genrateToken($data['user_id']);
            $response['data']['type']  = $data['type'];
            return $response;
        } else {
            throw new \App\Exceptions\ApiExceptions($message, 601);
        }
    }
    public function userRegistraion(Request $request){
        $params             = array();
        $params['mobile'] = $request->get('mobile');
        $params['name'] = $request->get('name');
        $params['email'] = $request->get('email');
        if($request->get('isVerified')){
            $params['is_verified'] = $request->get('isVerified');
        }

        $rule    = array(
            'mobile' => 'required|digits:10',
            'name' => 'required|string',
            'email' => 'required|email',
            'is_verified' => 'numeric|min:0|max:1'
        );
        $response = $this->commonModel->validator($params, $rule);
        if (!empty($response)) {
            return $response;
        }
        return $this->userEntity->getCreateUser($params);
    }

    public function addEditAddress(Request $request){
        $params             = array();
        $params['user_id'] = $request->get('userId');
        $addresses = $request->get('address');
        if(!$this->userEntity->validateUser($params['user_id'])){
            throw new \App\Exceptions\ApiExceptions('User not found', 601);
        }
        $locations = array();
        $rule    = array(
            'user_id' => 'required|numeric|gt:0',
            'location_id' => 'numeric|gte:0',
            'type' => 'required|string',
            'address' => 'required|string',
            'city_name' => 'required|string',
            'state' => 'required|string',
            'landmark' => 'string',
            'pincode' => 'required|digits:6',
        );
        foreach($addresses as $address){
            $params['location_id'] = $address['locationId'] ?? 0;
            $params['type'] = $address['type'];
            $params['address'] = $address['address'];
            $params['city_name'] = $address['cityName'];
            $params['state'] = $address['state'];
            $params['landmark'] = $address['landmark'];
            $params['pincode'] = $address['pincode'];
            $response = $this->commonModel->validator($params, $rule);
            if (!empty($response)) {
                return $response;
            }
            $locations[]=$params;
        }
        return $this->userEntity->addEditAddress($locations);
    }
}
