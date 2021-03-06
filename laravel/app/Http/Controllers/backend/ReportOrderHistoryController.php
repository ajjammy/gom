<?php
namespace App\Http\Controllers\backend;

use DB;
use App\User;
use App\Order;
use Hash;
use Excel;
use Validator;
use App\Helpers\DateFuncs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportOrderHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $users = $this->users();

        if (!empty($request->input('is_search'))) {
            $v = Validator::make($request->all(), [
                'type_sale_buy' => 'required',
                'user' => 'required',
            ]);
            if ($v->fails()) {
                return redirect()->back()->withErrors($v->errors());
            }
            $type_sale_buy = $request->input('type_sale_buy');
            $user_id = $request->input('user');
            $results = $this->sqlFilterShowPaginate($type_sale_buy, $user_id);
            return view('backend.reports.order_history_sale_buy', compact('users', 'results', 'type_sale_buy', 'user_id'));
        }
        return view('backend.reports.order_history_sale_buy', compact('users'));
    }

    public function exportExcel(Request $request)
    {
        if ($request->ajax()) {
            $type_sale_buy = $request->input('type_sale_buy');
            $user_id = $request->input('user_id');
            $filter_user = User::where('id', $user_id)->first();
            $filter_user_fname_lname = $filter_user->users_firstname_th. " ". $filter_user->users_lastname_th;
            $results = $this->sqlFilter($type_sale_buy, $user_id);
            if ($type_sale_buy == 'sale'){
                $i_sale_buy = trans('messages.i_sale');
                $arr[] = array(
                    trans('messages.order_id'),
                    trans('messages.order_date'),
                    trans('messages.order_type'),
                    trans('messages.i_buy'),
                    trans('messages.product_name'),
                    trans('messages.orderbyunit'),
                    trans('messages.order_total').'('.trans('messages.baht').')',
                    trans('messages.order_status'),
                );
            }
            if ($type_sale_buy == 'buy'){
                $i_sale_buy = trans('messages.i_buy');
                $arr[] = array(
                    trans('messages.order_id'),
                    trans('messages.order_date'),
                    trans('messages.order_type'),
                    trans('messages.i_sale'),
                    trans('messages.product_name'),
                    trans('messages.orderbyunit'),
                    trans('messages.order_total').'('.trans('messages.baht').')',
                    trans('messages.order_status'),
                );
            }
            foreach ($results as $v) {
                //$total_amount = $v->total_amount;
                $total_amount = $v->total;
                if($v->order_type== 'retail') {
                    $order_type = trans('messages.retail');
                }else {
                    $order_type = trans('messages.wholesale');
                }
                if ($type_sale_buy == 'sale'){
                    $fname_lname = $v->buyer->users_firstname_th. " ". $v->buyer->users_lastname_th;
                }
                if ($type_sale_buy == 'buy'){
                    $fname_lname = $v->user->users_firstname_th. " ". $v->user->users_lastname_th;
                }
                $order_date = DateFuncs::dateToThaiDate($v->order_date);
                $arr[] = array(
                    $v->id,
                    $order_date,
                    $order_type,
                    $fname_lname,
                    $v->product_name_th,
                    $v->quantity.' '.$v->units,
                    $total_amount,
                    $v->status_name
                );
            }

            $data = $arr;
            $info = Excel::create('order-history-sale-buy-excell', function($excel) use($data,$i_sale_buy,$filter_user_fname_lname) {
                $excel->sheet('Sheetname', function($sheet) use($data,$i_sale_buy,$filter_user_fname_lname) {
                    $sheet->mergeCells('A1:H1');
                    $sheet->mergeCells('A2:D3');
                    $sheet->mergeCells('E2:H3');
                    $sheet->setSize(array(
                        'A1' => array(
                            'height'    => 50
                        )
                    ));
                    $sheet->setAutoSize(array('A'));
                    $sheet->setColumnFormat(array(
                        'G' => '#,##0'
                    ));
                    $sheet->cells('A1', function($cells) {
                        $cells->setValue(trans('messages.text_report_menu_order_history_sale_buy'));
                        $cells->setValignment('center');
                        $cells->setAlignment('center');
                        $cells->setFont(array(
                            'size'       => '16',
                            'bold'       =>  true
                        ));
                    });
                    $sheet->cells('A2', function($cells) use($i_sale_buy) {
                        $cells->setValue(trans('messages.type_sale_buy').' : '.$i_sale_buy);
                        $cells->setFont(array(
                            'bold'       =>  true
                        ));
                        $cells->setValignment('center');
                    });

                    $sheet->cells('E2', function($cells) use($filter_user_fname_lname) {
                        $cells->setValue(trans('messages.member').' : '.$filter_user_fname_lname);
                        $cells->setFont(array(
                            'bold'       =>  true
                        ));
                        $cells->setValignment('center');
                    });


                    $sheet->rows($data);//fromArray
                });
            })->store('xls', false, true);
            return response()->json(array('file'=>$info['file']));
        }
    }

    private function sqlFilter($type_sale_buy = '',$userId=''){

        $result = Order::join('order_status', 'order_status.id', '=', 'orders.order_status');
        if ($type_sale_buy == 'sale') { //user_id sale
            $result->join('users', 'users.id', '=', 'orders.user_id');
        }
        if ($type_sale_buy == 'buy') { //user_id buy
            $result->join('users', 'users.id', '=', 'orders.buyer_id');
        }
        $result->join('order_items', 'order_items.order_id', '=', 'orders.id');
        $result->join('product_requests', 'product_requests.id', '=', 'order_items.product_request_id');
        $result->join('products', 'products.id', '=', 'product_requests.products_id');
        $result->select('orders.*', 'order_status.status_name'
            ,'users.users_firstname_th'
            ,'users.users_lastname_th'
            ,'products.product_name_th'
            ,'order_items.quantity'
            ,'product_requests.units'
            ,'order_items.total'
        );

        if ($type_sale_buy == 'sale') { //user_id sale
            $result->where('orders.user_id', $userId);
            $result->where('users.iwanttosale', $type_sale_buy);
        }
        if ($type_sale_buy == 'buy') { //buyer_id buy
            $result->where('orders.buyer_id', $userId);
            $result->where('users.iwanttobuy', $type_sale_buy);
        }
        $result->orderBy('orders.id', 'DESC');
        return $results = $result->get();
    }

    private function sqlFilterShowPaginate($type_sale_buy = '',$userId=''){

        $result = Order::join('order_status', 'order_status.id', '=', 'orders.order_status');
        if ($type_sale_buy == 'sale') { //user_id sale
            $result->join('users', 'users.id', '=', 'orders.user_id');
        }
        if ($type_sale_buy == 'buy') { //user_id buy
            $result->join('users', 'users.id', '=', 'orders.buyer_id');
        }
        $result->join('order_items', 'order_items.order_id', '=', 'orders.id');
        $result->join('product_requests', 'product_requests.id', '=', 'order_items.product_request_id');
        $result->join('products', 'products.id', '=', 'product_requests.products_id');
        $result->select('orders.*', 'order_status.status_name'
            ,'users.users_firstname_th'
            ,'users.users_lastname_th'
            ,'products.product_name_th'
            ,'order_items.quantity'
            ,'product_requests.units'
            ,'order_items.total'
        );

        if ($type_sale_buy == 'sale') { //user_id sale
            $result->where('orders.user_id', $userId);
            $result->where('users.iwanttosale', $type_sale_buy);
        }
        if ($type_sale_buy == 'buy') { //buyer_id buy
            $result->where('orders.buyer_id', $userId);
            $result->where('users.iwanttobuy', $type_sale_buy);
        }
        $result->orderBy('orders.id', 'DESC');
        return $results = $result->paginate(config('app.paginate'));
    }

    private function users(){
        return $users = User::select(DB::raw('users.id
            ,users.users_firstname_th
            ,users.users_lastname_th
            ,users.users_firstname_en
            ,users.users_lastname_en
            ,users.iwanttosale
            ,users.iwanttobuy'))
            ->where('is_active', 1)
            ->orderBy('users_firstname_th', 'ASC')->get();
    }
}