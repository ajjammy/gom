@extends('layouts.main')
@section('content')
@include('shared.search')
<div class="row marketmenuboxs">

  @foreach ($productCategoryitem as $key => $item)
  <a href="{{ url('market/?iwantto='.Request::input('iwantto').'&id='.Request::input('id').'&category='.$item->id) }}">
  <div class="col-md-2 col-sm-4">
      <div class="thumbnail">
          <div class="caption">
              <h4>
                  <p><i class="{{$item->productcategory_icon}} fa-2x" ></i></p>
                  <p>{{ $item->{ "productcategory_title_".Lang::locale()}  }}</p>
              </h4>
          </div>
      </div>
  </div>
</a>
  @endforeach
</div>
@stop


