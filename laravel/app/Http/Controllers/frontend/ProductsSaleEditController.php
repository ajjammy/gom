<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\OrderItem;
use App\Product;
use App\ProductCategory;
use App\ProductRequest;
use App\Province;
use App\Standard;
use App\Units;
use DB;
use File;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Validator;
use App\Helpers\DateFuncs;
use Illuminate\Support\Facades\Input;

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
        $showDelete =false;
        $provinceItem = Province::orderBy('PROVINCE_NAME', 'ASC')
            ->get();
        $grades = config('constants.grades');
        $standards = Standard::all();

        if ($id == 0) {
            $item = new ProductRequest();
            $item->id = 0;
            $item->productstatus = 'open';
            $item->iwantto = $useritem->iwanttosale;
            $item->products_id = '';
            $product_name = (object)array();
            $product_name->product_name_th = '';

        } else {
            $item = ProductRequest::find($id);

            $product_name = Product::where('id', '=', $item->products_id)->select('product_name_th')->first();

            $orderItems = OrderItem::where('product_request_id', $id)->get();
            if ($orderItems == null || $orderItems->count() < 1) {
                $showDelete = true;
            }

            if ($useritem->iwanttosale != $item->iwantto) {
                return redirect()->action('frontend\UserProfileController@index');
            }
        }

        $productCategoryitem = ProductCategory::orderBy('sequence', 'ASC')
            ->get();

        $unitsItem = Units::orderBy('sequence', 'ASC')
            ->get();


        for($i = 0;$i < $standards->count();$i++){
            $standards[$i]->checked = false;
            foreach ($item->standards as $standard){
                if ($standards[$i]->id == $standard->id){
                    $standards[$i]->checked = true;
                }
            }
        }

        /*echo $standards;
        exit();*/

        return view('frontend.productsaleedit', compact('item'
            , 'useritem', 'productCategoryitem', 'grades'
            , 'unitsItem', 'provinceItem', 'product_name' , 'standards' ,'showDelete'));
    }


    public function update(Request $request, $id)
    {


        $useritem = auth()->guard('user')->user();

        if ($id == 0) {
            $productRequest = new ProductRequest();
            $productRequest->id = 0;
        } else {
            $productRequest = ProductRequest::find($id);
        }

        if ($id == 0)
            $this->validate($request, $this->rules);
        else{
            if($request->is_packing != null && $request->is_packing == 1){

            }
            $this->validate($request, $this->rules3);
        }


        if ($request->product1_file != "") {
            $uploadImage1 = $this->UploadImage($request, 'product1_file');
            $this->RemoveFolderImage($productRequest->product1_file);
            $productRequest->product1_file = $uploadImage1["imageName"];
        }
        if ($request->product2_file != "") {
            $uploadImage2 = $this->UploadImage($request, 'product2_file');
            $this->RemoveFolderImage($productRequest->product2_file);
            $productRequest->product2_file = $uploadImage2["imageName"];
        }
        if ($request->product3_file != "") {
            $uploadImage3 = $this->UploadImage($request, 'product3_file');
            $this->RemoveFolderImage($productRequest->product3_file);
            $productRequest->product3_file = $uploadImage3["imageName"];
        }


        $productRequest->iwantto = $useritem->iwanttosale;
        $productRequest->product_title = $request->product_title;
        $productRequest->product_description = $request->product_description;
        $productRequest->price = $request->price;
        $productRequest->is_showprice = $request->is_showprice == "" ? 0 : 1;
        $productRequest->volumn = $request->volumn;
        $productRequest->productstatus = $request->productstatus;
        $productRequest->units = $request->units;
        $productRequest->city = $request->city;
        $productRequest->province = $request->province;
        $productRequest->productcategorys_id = $request->productcategorys_id;
        $productRequest->products_id = $request->products_id;
        $productRequest->users_id = $useritem->id;
        $productRequest->grade = $request->grade;
        $productRequest->is_packing = $request->is_packing;
        $productRequest->packing_size = $request->packing_size;
        $productRequest->province_selling = $request->province_selling;
        $productRequest->start_selling_date = DateFuncs::convertThaiDateToMysql($request->start_selling_date);
        $productRequest->end_selling_date = DateFuncs::convertThaiDateToMysql($request->end_selling_date);
        $productRequest->selling_period =  $request->selling_period;
        $productRequest->selling_type = $request->selling_type;

        $arr_checked_product_standards = Input::get('product_standard');

        if ($id == 0) {
            $productRequest->save();
            //Save to product_request_standard :: many to many relationship
            if(is_array($arr_checked_product_standards)){
                foreach ($arr_checked_product_standards as $item){
                    $productRequest->standards()->save(Standard::find($item));
                }
            }
            $id = $productRequest->id;
        } else {
            if(is_array($arr_checked_product_standards)){
                $productRequest->standards()->detach();
                foreach ($arr_checked_product_standards as $item){
                    $productRequest->standards()->save(Standard::find($item));
                }
            }
            $productRequest->update();
        }

//        $itemsbuy = $productRequest->GetSaleMatchingWithBuy($useritem->id, '');
//        $itemssale = $productRequest->GetBuyMatchingWithSale($useritem->id, '');
//
//        foreach ($itemsbuy as $div_item) {
//            $this->SendEmailMatching($div_item);
//        }
//
//        foreach ($itemssale as $div_item) {
//            $this->SendEmailMatching($div_item);
//        }

        return redirect()->route('productsaleedit.show', ['id' => $id])
            ->with('success', trans('messages.message_update_success'));
    }

    public function destroy($id)
    {
        $deleteItem = ProductRequest::find($id);

        $deleteItem->delete();

        if (isset($deleteItem->product1_file))
        {
            $this->RemoveFolderImage($deleteItem->product1_file);
        }

        if (isset($deleteItem->product2_file))
        {
            $this->RemoveFolderImage($deleteItem->product2_file);
        }

        if (isset($deleteItem->product3_file))
        {
            $this->RemoveFolderImage($deleteItem->product3_file);
        }

        return redirect()->route('user.iwanttosale.index')
            ->with('success', trans('messages.message_delete_success'));
    }

    private function SendEmailMatching($div_item)
    {
        $sendemailTo = $div_item->email;
        $sendemailFrom = env('MAIL_USERNAME');

        $data = array(
            'fullname' => $div_item->users_firstname_th . " " . $div_item->users_lastname_th
        );
        sleep(0.1);
        Mail::send('emails.matching', $data, function ($message) use ($sendemailTo, $sendemailFrom) {
            $message->from($sendemailFrom
                , "Greenmart Online Market");
            $message->to($sendemailTo)
                ->subject("Greenmart Online Market : " . trans('messages.email_subject_matching'));

        });
    }

    private function RemoveFolderImage($rawfile)
    {
        sleep(1);
        if ($rawfile != "") {
            $rawfileArr = explode("/", $rawfile);
            $indexFile = count($rawfileArr) - 1;
            $indexFolder = count($rawfileArr) - 2;
            File::delete($rawfile);
            File::deleteDirectory(config('app.upload_product') . $rawfileArr[$indexFolder]);
        }
    }

    private function UploadImage(Request $request, $imagecolumnname)
    {
        sleep(1);
        $fileTimeStamp = time();
        $imageTempName = $request->file($imagecolumnname)->getPathname();

        $imageName = $request->{$imagecolumnname}->getClientOriginalName();

        //$imageName_temp = iconv('UTF-8', 'tis620',$imageName);
        $imageName_temp = $imageName;

        $request->{$imagecolumnname}->move(config('app.upload_product') . $fileTimeStamp . "/", $imageName_temp);
        $imageName = config('app.upload_product') . $fileTimeStamp . "/" . $imageName;

        return array('imageTempName' => $imageTempName, 'imageName' => $imageName);
    }
}
