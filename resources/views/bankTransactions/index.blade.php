@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Bank Transactions
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="{{ url('/bank_transactions/create' )}}">New</a>
                    </div>
                </div>

                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>
                                    Bank Name
                                </th>
                                <th>
                                    Account No.
                                </th>
                                <th>
                                    Transaction Type
                                </th>
                                <th>
                                    Amount
                                </th>
                                <th>
                                    Date
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(sizeof($transactions)>0)
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td>
                                            {{ $transaction->bank->name }}
                                        </td>
                                        <td>
                                            {{ $transaction->bank->account_no }}
                                        </td>
                                        <td>
                                            {{ Config::get('common.bank_transaction_type')[$transaction->transaction_type] }}
                                        </td>
                                        <td>
                                            {{ $transaction->amount }}
                                        </td>
                                        <td>
                                            {{ $transaction->transaction_date }}
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
                    <div class="pagination"> {{ $transactions->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
