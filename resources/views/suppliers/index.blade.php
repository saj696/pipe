@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Suppliers
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="{{ url('/suppliers/create' )}}">Add New Suppliers</a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th> Title </th>
                                    <th>Supplier Type</th>
                                    <th>Company Office phone</th>
                                    <th>Contact Person</th>
                                    <th>Contact Person Phone No.</th>
                                    <th> Status </th>
                                    <th> Action </th>
                                </tr>
                            </thead>
                            <tbody>

                            @if(sizeof($suppliers)>0)
                            @foreach($suppliers as $supplier)
                            <tr>
                                <td>{{ $supplier->company_name }}</td>
                                <td>{{ \Illuminate\Support\Facades\Config::get('common.supplier_types')[$supplier->suppliers_type] }}</td>
                                <td>{{ $supplier->company_office_phone }}</td>
                                <td>{{ $supplier->contact_person }}</td>
                                <td>{{ $supplier->contact_person_phone }}</td>
                                <td>{{ \Illuminate\Support\Facades\Config::get('common.status')[$supplier->status] }}</td>
                                <td>
                                    <a class="label label-danger" href="{{ url('/suppliers/'.$supplier->id.'/edit' )}}">Edit</a>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="7" class="text-center danger">No Data Found</td>
                            </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination"> {{ $suppliers->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
