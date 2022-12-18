<?php

namespace App\Interfaces;



interface UserRepositoryInterface
{

    public function SendSms($phone ,$token);
    public function SavePhoneToken(array $data);
    public function CheckSmsToken($phone , $token);
    public function SignIn($username , $password);
    public function SetNewPassword($id,$password);
    public function upload($file);
    public function generateCode();
    public function GetCountOfUserProgress($id);
    public function GetIdentificationUser($identification);
    public function UserPurchasedProductsCount($user_id);
    public function UserPurchasedProducts($user_id);

}