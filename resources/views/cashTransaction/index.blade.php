@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Cash Transfer
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="{{ url('/cash_transaction/create' )}}">Send Cash</a>
                    </div>
                </div>

                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        Sending Date
                                    </th>
                                    <th>
                                        Transaction Type
                                    </th>
                                    <th>
                                        Workspace
                                    </th>
                                    <th>
                                        Amount
                                    </th>
                                    <th>
                                        Receiving Date
                                    </th>
                                    <th class="text-center">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(sizeof($transactions)>0)
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td>
                                            {{ $transaction->sending_date }}
                                        </td>
                                        <td>
                                            {{ $transaction->workspace_from==Auth::user()->workspace_id?'Send':'Receive' }}
                                        </td>
                                        <td>
                                            {{ $transaction->workspace_from==Auth::user()->workspace_id?$workspaces[$transaction->workspace_to]:$workspaces[$transaction->workspace_from] }}
                                        </td>
                                        <td>
                                            {{ $transaction->amount }}
                                        </td>
                                        <td>
                                            {{ (strtotime($transaction->receiving_date) >= strtotime($transaction->sending_date))?$transaction->receiving_date:'Not Yet' }}
                                        </td>
                                        <td class="text-center">
                                            @if($transaction->workspace_from==Auth::user()->workspace_id && $transaction->received==0)
                                                <label class="label label-warning">{{ 'Not Received' }}</label>
                                            @elseif($transaction->workspace_from==Auth::user()->workspace_id && $transaction->received==1)
                                                <label class="label label-success">{{ 'Received' }}</label>
                                            @elseif($transaction->workspace_to==Auth::user()->workspace_id && $transaction->received==1)
                                                <label class="label label-success">{{ 'Received' }}</label>
                                            @elseif($transaction->workspace_to==Auth::user()->workspace_id && $transaction->received==0)
                                                <a class="label label-danger" href="{{ url('/cash_transaction/'.$transaction->id.'/edit' )}}">Receive</a>
                                            @endif
                                        </td>
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
                    <div class="pagination"> {{ $transactions->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
