<?php

namespace App\Model\frontend;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'users_firstname_th','users_lastname_th','users_firstname_en','users_lastname_en'
        ,'users_dateofbirth','users_gender','users_addressname','users_street','users_district'
        ,'users_city','users_province','users_postcode','users_mobilephone'
        ,'users_phone','users_fax','users_imageprofile'
        ,'users_latitude','users_longitude','users_contactperson'
        ,'users_membertype','iwantto','users_idcard'
        ,'is_active'
        , 'email'
        , 'password'
        ,'users_qrcode'
        ,'users_taxcode'
        ,'users_company_th'
        ,'users_company_en'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function registeruser($input = array()) {
            return User::create([
                    'users_firstname_th' => $input['users_firstname_th'],
                    'users_lastname_th' => $input['users_lastname_th'],
                    'users_firstname_en' => $input['users_firstname_en'],
                    'users_lastname_en' => $input['users_lastname_en'],
                    'users_dateofbirth' => $input['users_dateofbirth'],
                    'users_gender' => $input['users_gender'],
                    'users_addressname' => $input['users_addressname'],
                    'users_street' => $input['users_street'],
                    'users_district' => $input['users_district'],
                    'users_city' => $input['users_city'],
                    'users_province' => $input['users_province'],
                    'users_postcode' => $input['users_postcode'],
                    'users_mobilephone' => $input['users_mobilephone'],
                    'users_phone' => $input['users_phone'],
                    'users_fax' => '',
                    'users_imageprofile' => '',
                    'users_latitude' => 0,
                    'users_longitude' => 0,
                    'users_contactperson' => '',
                    'users_membertype' => 'personal',
                    'iwantto' => $input['iwantto'],
                    'users_idcard' => $input['users_idcard'],
                    'is_active' => 0,
                    'email' => $input['email'],
                    'password' => bcrypt($input['password']),
                    'users_qrcode' => $input['users_qrcode'],
                    'users_taxcode' => '',
                    'users_company_th' => '',
                    'users_company_en' => '',
                ]);
    }

    public static function companyregisteruser($input = array()) {
            return User::create([
                    'users_firstname_th' => '',
                    'users_lastname_th' => '',
                    'users_firstname_en' => '',
                    'users_lastname_en' => '',
                    'users_dateofbirth' => '1976-01-01',
                    'users_gender' => 'male',
                    'users_addressname' => $input['users_addressname'],
                    'users_street' => $input['users_street'],
                    'users_district' => $input['users_district'],
                    'users_city' => $input['users_city'],
                    'users_province' => $input['users_province'],
                    'users_postcode' => $input['users_postcode'],
                    'users_mobilephone' => $input['users_mobilephone'],
                    'users_phone' => $input['users_phone'],
                    'users_fax' => $input['users_fax'],
                    'users_imageprofile' => '',
                    'users_latitude' => 0,
                    'users_longitude' => 0,
                    'users_contactperson' => '',
                    'users_membertype' => 'company',
                    'iwantto' => $input['iwantto'],
                    'users_idcard' => '',
                    'is_active' => 0,
                    'email' => $input['email'],
                    'password' => bcrypt($input['password']),
                    'users_qrcode' => '',
                    'users_taxcode' => $input['users_taxcode'],
                    'users_company_th' => $input['users_company_th'],
                    'users_company_en' => $input['users_company_en'],
                ]);
    }

}