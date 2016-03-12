@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Financial Year
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="{{ url('/financial_year/create' )}}">New</a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>
                                    Sl. No.
                                </th>
                                <th>
                                    Year
                                </th>
                                <th>
                                    Start Date
                                </th>
                                <th>
                                    End Date
                                </th>
                                <th>
                                    Status
                                </th>
                               {{-- <th>
                                    Action
                                </th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i=1; ?>
                            @if(sizeof($financialYears)>0)
                                @foreach($financialYears as $financialYear)
                                    <tr>
                                        <td>
                                              {{ $i++ }}
                                        </td>
                                        <td>
                                            {{ $financialYear->year }}
                                        </td>
                                        <td>
                                            {{ date('d-m-Y',$financialYear->start_date) }}
                                        </td>
                                        <td>
                                            {{ date('d-m-Y',$financialYear->end_date) }}
                                        </td>
                                        <td>
                                            {{ ($financialYear->status)? "Active" : "In-Active" }}
                                        </td>
                                        {{--<td>
                                            <a class="label label-danger"
                                               href="{{ url('/financial_year/'.$financialYear->id.'/edit' )}}">Edit</a>
                                        </td>--}}
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center danger">No Data Found</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination"> {{ $financialYears->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
