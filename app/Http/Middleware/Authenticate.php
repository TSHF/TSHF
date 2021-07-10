<?php

/**
 * Description of AuthenticationHeader
 *
 * @author mohit
 */

namespace App\Http\Middleware;

use App\Http\Entities\UserEntity;
use Closure;
use Illuminate\Support\Carbon;
use \Illuminate\Support\Facades\Config;

class Authenticate
{

    protected $userentity;
    public function __construct(UserEntity $userentity)
    {
        $this->userEntity = $userentity;
    }

    public function handle($request, Closure $next)
    {
        $userDetail = array();
        $token      = isset($_SERVER['HTTP_TOKEN']) ? $_SERVER['HTTP_TOKEN'] : '';
        $ip         = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

        if ($request->get('class') == 'order') {
            $userDetail = $this->userEntity->getUserByToken($token);
            if (!empty((array) $userDetail)) {
                $jsonParameter               = $request->json()->all();
                $jsonParameter['userDetail'] = $userDetail;
                $request->merge($jsonParameter);
            } else {
                throw new \App\Exceptions\ApiExceptions('', 521);
            }
        }

        $response = $next($request);

        if ($response->exception) {
            return $response;
        }

        $content               = json_decode($response->getContent(), true);
        $content['status']     = config("constants.STATUS_SUCCESS");
        $content['statusCode'] = config("constants.STATUS_CODE");
        $content['timestamp']  = Carbon::now()->toDateTimeString();
        $response->setContent(json_encode($content));

        return $response;
    }

}
