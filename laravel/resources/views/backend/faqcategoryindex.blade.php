<?php
$url = "admin/faqcategory";
$pagetitle = trans('messages.menu_faqcategory');
?>
@extends('layouts.dashboard')
@section('page_heading',$pagetitle)
@section('page_heading_image','<i class="glyphicon glyphicon-question-sign"></i>')
@section('section')

<div class="col-sm-12">
  <div class="row">
  	<div class="col-sm-12">
      @if ($message = Session::get('success'))
          <div class="alert alert-success">
              <p>{{ $message }}</p>
          </div>
      @endif
      @if ($message = Session::get('error'))
          <div class="alert alert-danger">
              <p>{{ $message }}</p>
          </div>
      @endif
      <div class="panel panel-default">
        <div class="panel-body">
              {!! Form::open(['method'=>'GET','url'=>$url,'class'=>'','role'=>'search'])  !!}
              <div class="input-group custom-search-form">
                  <input type="text" id="search" name="search" class="form-control" placeholder="{{ trans('messages.search') }}
...">
                  <span class="input-group-btn">
                      <button class="btn btn-default" type="submit">
                          <i class="fa fa-search"></i>
                      </button>
                  </span>
              </div>
              {!! Form::close() !!}
              <p></p>
              <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                      <tr>
                          <th>{{ trans('messages.no') }}</th>
                          <th>{{ Lang::get('validation.attributes.faqcategory_title_th') }}</th>
                          <th>{{ Lang::get('validation.attributes.faqcategory_description_th') }}</th>
                          <th>{{ Lang::get('validation.attributes.faqcategory_title_en') }}</th>
                          <th>{{ Lang::get('validation.attributes.faqcategory_description_en') }}</th>
                          <th>{{ Lang::get('validation.attributes.sequence') }}</th>
                          <th width="150px" style="text-align:center;">
                            <a class="btn btn-success" href="{{ route('faqcategory.create') }}">
                              <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                            </a>
                          </th>
                      </tr>
                    </thead>
                    <tbody>
                @foreach ($items as $key => $item)
                      <tr>
                          <td>{{ ++$i }}</td>
                          <td>{{ $item->faqcategory_title_th }}</td>
                          <td>{!! $item->faqcategory_description_th !!}</td>
                          <td>{{ $item->faqcategory_title_en }}</td>
                          <td>{!! $item->faqcategory_description_en !!}</td>
                          <td>{{ $item->sequence }}</td>
                          <td style="text-align:center;">
                              <a class="btn btn-primary" href="{{ url ('admin/faq?faqcategory='.$item->id) }}">
                                <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>
                              </a>
                              <a class="btn btn-primary" href="{{ route('faqcategory.edit',$item->id) }}">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                              </a>
                              <?php
                                $confirmdelete = trans('messages.confirm_delete', ['attribute' => $item->faqcategory_title_th]);
                              ?>
                              {!! Form::open(['method' => 'DELETE','route' => ['faqcategory.destroy', $item->id],'style'=>'display:inline']) !!}

                              <button onclick="return confirm('{{$confirmdelete}}');"  class="btn btn-danger" type="submit">
                                  <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                              </button>

                              {!! Form::close() !!}
                          </td>
                      </tr>
                @endforeach
                    </tbody>
                </table>
              </div>

            {!! $items->appends(Request::all()) !!}
        </div>
		  </div>
    </div>
  </div>
</div>
@endsection
