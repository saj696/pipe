<?php

namespace App\Http\Controllers;


use App\Http\Requests;
use App\Models\Module;
use App\Article;
use App\Tag;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class AjaxController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function getModules(Request $request)
    {
        $component_id = $request->input('component_id');
        $data = Module::where('component_id', '=', $component_id)->lists('name_en', 'id');;
        return response()->json($data);
    }
}
