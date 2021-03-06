<?php

if($mode=="create")
{
  $method = "POST";
  $formModelId = 0;
  $controllerAction = "badword.store";
}
else
{
  $method = "PATCH";
  $formModelId =  $item->id;
  $controllerAction = "badword.update";
}
?>
@extends('layouts.dashboard')
@section('page_heading',trans('messages.menu_bad_word'))
@section('page_heading_image','<i class="glyphicon glyphicon-apple"></i>')
@section('section')
<div class="col-sm-12">

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>{{ trans('messages.message_whoops_error')}}</strong> {{ trans('messages.message_result_error')}}
            <br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {!! Form::model($item, ['method' => $method,'route' => [$controllerAction, $formModelId]]) !!}

    <div class="row">

        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group {{ $errors->has('product_name_th') ? 'has-error' : '' }}">
                <strong>* {{ Lang::get('validation.attributes.product_name_th') }}:</strong>
                {!! Form::text('product_name_th', null, array('placeholder' => Lang::get('validation.attributes.product_name_th'),'class' => 'form-control')) !!}
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group {{ $errors->has('product_name_en') ? 'has-error' : '' }}">
                <strong>* {{ Lang::get('validation.attributes.product_name_en') }}:</strong>
                {!! Form::text('product_name_en', null, array('placeholder' => Lang::get('validation.attributes.product_name_en'),'class' => 'form-control')) !!}
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group {{ $errors->has('sequence') ? 'has-error' : '' }}">
                <label class="col-sm-2 control-label" style="padding-left: 0px;">
                  <strong>{{ Lang::get('validation.attributes.sequence') }}:</strong>
                </label>
                <div class="col-sm-2" style="padding-left: 0px;">
                {!! Form::number('sequence', null, array('placeholder' => Lang::get('validation.attributes.sequence'),'style' => 'text-align:center;','class' => 'form-control')) !!}
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12" >
               <input type="hidden" id="productcategory_id" name="productcategory_id" value="<?php echo $_REQUEST["productcategory"]; ?>" />
                <button type="submit" class="btn btn-primary">
                  <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                  {{ trans('messages.button_save')}}</button>
        </div>

    </div>
    {!! Form::close() !!}
</div>
@endsection
