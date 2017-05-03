<?php
use App\ProductRequest;

$user = auth()->guard('user')->user();

$productRequest = new ProductRequest();
?>
<ul class="nav nav-tabs">
    {{--<li role="presentation" {{ ($setActive == "userprofiles")? 'class=active' : ''  }} ><a href="{{ url('user/userprofiles') }}">{{ trans('messages.userprofile') }}</a></li>
    <li role="presentation" {{ ($setActive == "changepasswords")? 'class=active' : ''  }} ><a href="{{ url('user/changepasswords') }}">{{ trans('messages.menu_changepassword') }}</a></li>
    <li role="presentation" {{ ($setActive == "inboxmessage")? 'class=active' : ''  }}><a href="{{ url('user/inboxmessage') }}">
            {{ trans('messages.inbox_message') }}
            <span class="badge">0</span>
            </a>
    </li>--}}

    <li role="presentation" {{ ($setActive == "matchings")? 'class=active' : ''  }} >
        <a href="{{ url('user/matchings') }}">
            {{ trans('messages.menu_matching') }}
            <span class="badge">
                           @if(($user->iwanttobuy == "buy")&&($user->iwanttosale == "sale"))
                    {{ count($productRequest->GetSaleMatchingWithBuy($user->id,Request::input('orderby'))) + count($productRequest->GetBuyMatchingWithSale($user->id,Request::input('orderby'))) }}
                @endif
                @if(($user->iwanttobuy == "buy")&&($user->iwanttosale == ""))
                    {{ count($productRequest->GetSaleMatchingWithBuy($user->id,Request::input('orderby'))) }}
                @endif
                @if(($user->iwanttobuy == "")&&($user->iwanttosale == "sale"))
                    {{ count($productRequest->GetBuyMatchingWithSale($user->id,Request::input('orderby'))) }}
                @endif
                           </span>
        </a>
    </li>

    @if($user->iwanttobuy == "buy")
        <li role="presentation" {{ ($setActive == "iwanttobuy")? 'class=active' : ''  }}><a
                    href="{{ url('user/iwanttobuy') }}"> {{ trans('messages.i_want_to_buy') }}</a></li>
        <li role="presentation" {{ ($setActive == "order")? 'class=active' : ''  }}><a
                    href="{{ url('user/order/') }}"> {{ trans('messages.menu_order_list') }}</a></li>
    @endif
    @if($user->iwanttosale == "sale")
        <li role="presentation" {{ ($setActive == "iwanttosale")? 'class=active' : ''  }}><a
                    href="{{ url('user/iwanttosale') }}"> {{ trans('messages.i_want_to_sale') }}</a></li>

        <li role="presentation" {{ ($setActive == "shoporder")? 'class=active' : ''  }}><a
                    href="{{ url('user/shoporder/') }}"> {{ trans('messages.menu_shop_order_list') }}</a></li>

        <li role="presentation" {{ ($setActive == "addproduct")? 'class=active' : ''  }}><a
                    href="{{ url('user/userproduct') }}"> {{ trans('messages.menu_add_product') }}</a></li>
        <li role="presentation" {{ ($setActive == "promotion")? 'class=active' : ''  }}><a
                    href="{{ url('user/promotion') }}"> {{ trans('messages.menu_promotion') }}</a></li>
        <li role="presentation" {{ ($setActive == "shopsetting")? 'class=active' : ''  }}><a
                    href="{{ url('user/shopsetting') }}"> {{ trans('messages.shop_setting') }}</a></li>
    @endif
</ul>