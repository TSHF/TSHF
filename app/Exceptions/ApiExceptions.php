<?php


namespace App\Exceptions;
 
use Exception;
use Illuminate\Support\Carbon;
 
class ApiExceptions extends Exception
{
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
    }

    public function returnJsonError($exception)
    {
        $exceptionCode =500;
        $errorMessage='Some error occured.Please try again later.';
        if($exception->getCode()){
            $exceptionCode=$exception->getCode();
        }
        if(!$exception->getMessage()){
            $errorMessage=config("constants.errorCodes")[$exceptionCode];
        }elseif($exception->getMessage()){
            $errorMessage=$exception->getMessage();
        }
        
        return response()->json(['message'=>$errorMessage,'status'=>'error','statusCode'=>$exceptionCode,'timestamp'=>Carbon::now()->toDateTimeString()]);
    }
}