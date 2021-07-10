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
}
