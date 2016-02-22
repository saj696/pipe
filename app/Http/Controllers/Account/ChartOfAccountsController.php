<?php

namespace App\Http\Controllers\Account;

use App\Http\Requests;
use App\Models\ChartOfAccount;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChartOfAccountRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Session;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class ChartOfAccountsController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $charts = ChartOfAccount::where('status', 1)->get();

        $accounts = ['cid'=>[], 'parent'=>[]];

        foreach($charts as $chart)
        {
            $accounts['cid'][$chart->id] = $chart;
            $accounts['parent'][$chart->parent][] = $chart->id;
        }

        $html = $this->buildChartTree(0, $accounts);
        return view('chartOfAccounts.index', compact('html'));
    }

    public function show($id)
    {
//        $component = Component::findOrFail($id);
//        return view('components.show', compact('component'));
    }

    public function create()
    {
        $parents = ChartOfAccount::where('status', 1)->lists('name', 'id');
        return view('chartOfAccounts.create', compact('parents'));
    }

    public function store(ChartOfAccountRequest $request)
    {
        $chart = New ChartOfAccount;
        $chart->parent = $request->input('parent');
        $chart->name = $request->input('name');
        $chart->code = $request->input('code');
        if($request->input('contra_status'))
        {
            $chart->contra_status = $request->input('code');
            $chart->contra_id = $request->input('contra_id');
        }
        $chart->save();

        Session()->flash('flash_message', 'Chart Of Account has been created!');
        return redirect('charts');
    }

    public function edit($id)
    {
        $parents = ChartOfAccount::where('status', 1)->lists('name', 'id');
        $chart = ChartOfAccount::findOrFail($id);
        return view('chartOfAccounts.edit', compact('chart', 'parents'));
    }

    public function update($id, ChartOfAccountRequest $request)
    {
        $chart = ChartOfAccount::findOrFail($id);
        $chart->parent = $request->input('parent');
        $chart->name = $request->input('name');
        $chart->code = $request->input('code');
        $chart->status = $request->input('status');
        if($request->input('contra_status'))
        {
            $chart->contra_status = $request->input('code');
            $chart->contra_id = $request->input('contra_id');
        }
        $chart->update();

        Session()->flash('flash_message', 'Chart Of Account has been updated!');
        return redirect('charts');
    }

    public function buildChartTree($parent, $accounts)
    {
        $html = "";
        if(isset($accounts['parent'][$parent]))
        {
            $html .= "<ul>";
            foreach($accounts['parent'][$parent] as $ca)
            {
                if(!isset($accounts['parent'][$ca]))
                {
                    $html .= "<li style='margin: 5px;'>" . "<label style='padding: 2px 8px 2px 8px;' class='btn btn-circle red'><a style='color:white;' href='".url('/charts/'.$accounts['cid'][$ca]->id.'/edit' )."'>". $accounts['cid'][$ca]->name . "</a></label>". "</li>";
                }
                if(isset($accounts['parent'][$ca]))
                {
                    $html .= "<li style='margin: 5px;'>" . "<label style='padding: 2px 8px 2px 8px;' class='btn btn-circle green'><a style='color:white;' href='".url('/charts/'.$accounts['cid'][$ca]->id.'/edit' )."'>" . $accounts['cid'][$ca]->name . "</a></label>";
                    $html .= $this->buildChartTree($ca, $accounts);
                    $html .= "</li>";
                }
            }
            $html .= "</ul>";
        }
        return $html;
    }
}
