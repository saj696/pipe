<?php

namespace App\Http\Controllers\Report;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PrintReportController extends Controller
{
    public function index()
    {
        return view('reports.printScript.print');
    }
}
