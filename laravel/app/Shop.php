<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    //
    protected $table = "shops";
    public $fillable = ['user_id',
                        'shop_title',
                        'shop_subtitle',
                        'shop_description',
                        'image_file_1',
                        'image_file_2',
                        'image_file_3',
                        'theme'
                    ];

}