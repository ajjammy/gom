<?php

namespace App\Http\Controllers\Admin;

use File;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AboutUs;

class AboutUsController extends Controller
{
    private $rules = [
       'aboutus_description_th' => 'required',
       'aboutus_description_en' => 'required',
    ];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data = array('mode' => 'edit');
        $item = AboutUs::find(1);
        if($item == null)
          $item = new AboutUs();
        return view('admin.aboutusedit',compact('item'))->with($data);
    }

    public function update(Request $request)
    {
        $this->validate($request, $this->rules);
        $item = AboutUs::find(1);
        if($item == null)
            AboutUs::create($request->all());
        else
            AboutUs::find(1)->update($request->all());

        return redirect()->route('aboutus.index')
                        ->with('success',trans('messages.message_update_success'));
    }
}