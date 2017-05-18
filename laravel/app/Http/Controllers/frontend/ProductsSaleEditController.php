<?php

namespace App\Http\Controllers\frontend;

use File;
use DB;
use Hash;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Model\frontend\User;
use App\Iwantto;
use App\ProductCategory;
use App\Units;
use App\Amphur;
use App\Province;
use App\District;
use App\Product;

class ProductsSaleEditController extends Controller
{
  private $rules = [
     'productcategorys_id' => 'required',
     'products_id' => 'required',
     //'product_title' => 'required',
     //'product_description' => 'required',
     'price' => 'required|numeric',
     'volumn' => 'required|numeric',
     'units' => 'required',
     'city' => 'required',
     'province' => 'required',
     'product1_file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:3048',
     'product2_file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:3048',
     'product3_file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:3048',
  ];

  private $rules2 = [
     'productcategorys_id' => 'required',
     'products_id' => 'required',
     //'product_title' => 'required',
     //'product_description' => 'required',
     'pricerange_start' => 'required|numeric',
     'pricerange_end' => 'required|numeric',
     'volumnrange_start' => 'required|numeric',
     'volumnrange_end' => 'required|numeric',
     'units' => 'required',
     'city' => 'required',
     'province' => 'required',
  ];

  private $rules3 = [
     'productcategorys_id' => 'required',
     'products_id' => 'required',
     //'product_title' => 'required',
     //'product_description' => 'required',
     'price' => 'required|numeric',
     'volumn' => 'required|numeric',
     'units' => 'required',
     'city' => 'required',
     'province' => 'required',
     'product1_file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:3048',
     'product2_file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:3048',
     'product3_file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:3048',
  ];


  public function __construct()
  {
      $this->middleware('user');
  }

  public function show($id)
  {
    $useritem = auth()->guard('user')->user();
    $provinceItem = Province::orderBy('PROVINCE_NAME','ASC')
                ->get();

    if($id == 0)
    {
      $item = new Iwantto();
      $item->id = 0;
      $item->productstatus ='open';
      $item->iwantto = $useritem->iwanttosale;
	  $item->products_id = '';
	  $product_name = (object)array();
	  $product_name->product_name_th = '';
    }
    else {
      $item = Iwantto::find($id);
	  
	  $product_name = Product::where('id', '=', $item->products_id)->select('product_name_th')->first();

      if($useritem->iwanttosale != $item->iwantto )
      {
        return redirect()->action('frontend\UserProfileController@index');
      }
    }

    $productCategoryitem = ProductCategory::orderBy('sequence','ASC')
                ->get();

    $unitsItem = Units::orderBy('sequence','ASC')
                ->get();

    return view('frontend.productsaleedit',compact('item'
                        ,'useritem','productCategoryitem'
                        ,'unitsItem','provinceItem','product_name'));
  }


  public function update(Request $request, $id)
  {
    $useritem = auth()->guard('user')->user();

    if($id==0)
    {
      $Iwantto = new Iwantto();
      $Iwantto->id = 0;
    }
    else {
      $Iwantto = Iwantto::find($id);
    }

    if($id==0)
      $this->validate($request, $this->rules);
    else
      $this->validate($request, $this->rules3);

    if($request->product1_file != "")
    {
      $uploadImage1 = $this->UploadImage($request, 'product1_file');
      $this->RemoveFolderImage($Iwantto->product1_file);
      $Iwantto->product1_file = $uploadImage1["imageName"];
    }
    if($request->product2_file != "")
    {
      $uploadImage2 = $this->UploadImage($request, 'product2_file');
      $this->RemoveFolderImage($Iwantto->product2_file);
      $Iwantto->product2_file = $uploadImage2["imageName"];
    }
    if($request->product3_file != "")
    {
      $uploadImage3 = $this->UploadImage($request, 'product3_file');
      $this->RemoveFolderImage($Iwantto->product3_file);
      $Iwantto->product3_file = $uploadImage3["imageName"];
    }


    $Iwantto->iwantto = $useritem->iwanttosale;
    $Iwantto->product_title = $request->product_title;
    $Iwantto->product_description = $request->product_description;
    $Iwantto->guarantee = $request->guarantee;
    $Iwantto->price = $request->price;
    $Iwantto->is_showprice = $request->is_showprice == ""? 0 : 1;
    $Iwantto->volumn = $request->volumn;
    $Iwantto->productstatus = $request->productstatus;
    $Iwantto->units = $request->units;
    $Iwantto->city = $request->city;
    $Iwantto->province = $request->province;
    $Iwantto->productcategorys_id = $request->productcategorys_id;
    $Iwantto->products_id = $request->products_id;
    $Iwantto->users_id = $useritem->id;

    if($id==0)
    {
      $Iwantto->save();
      $id = $Iwantto->id;
    }
    else {
      $Iwantto->update();
    }

    $itemsbuy = $Iwantto->GetSaleMatchingWithBuy($useritem->id, '');
    $itemssale = $Iwantto->GetBuyMatchingWithSale($useritem->id, '');

    foreach($itemsbuy as $div_item)
    {
        $this->SendEmailMatching($div_item);
    }

    foreach($itemssale as $div_item)
    {
        $this->SendEmailMatching($div_item);
    }


    return redirect()->route('productsaleedit.show', ['id' => $id])
                    ->with('success',trans('messages.message_update_success'));
  }

  private function SendEmailMatching($div_item)
  {
    $sendemailTo = $div_item->email;
    $sendemailFrom = env('MAIL_USERNAME');

    $data = array(
        'fullname' => $div_item->users_firstname_th." ".$div_item->users_lastname_th
    );
    sleep(0.1);
    Mail::send('emails.matching', $data, function ($message) use($sendemailTo, $sendemailFrom)
    {
        $message->from($sendemailFrom
                , "Greenmart Online Market");
        $message->to($sendemailTo)
                ->subject("Greenmart Online Market : ".trans('messages.email_subject_matching'));

    });
  }

  private function RemoveFolderImage($rawfile)
  {
      sleep(1);
      if($rawfile != "")
      {
        $rawfileArr = explode("/", $rawfile);
        $indexFile = count($rawfileArr) - 1;
        $indexFolder = count($rawfileArr) - 2;
        File::delete($rawfile);
        File::deleteDirectory(config('app.upload_product').$rawfileArr[$indexFolder]);
      }
  }

  private function UploadImage(Request $request, $imagecolumnname)
  {      
	  sleep(1);
      $fileTimeStamp = time();
      $imageTempName = $request->file($imagecolumnname)->getPathname();

      $imageName = $request->{ $imagecolumnname }->getClientOriginalName();
	  
	  //$imageName_temp = iconv('UTF-8', 'tis620',$imageName);
	  $imageName_temp = $imageName;

      $request->{ $imagecolumnname }->move(config('app.upload_product').$fileTimeStamp."/", $imageName_temp);
      $imageName = config('app.upload_product').$fileTimeStamp."/".$imageName;

      return array('imageTempName'=> $imageTempName, 'imageName' => $imageName);
  }
}
