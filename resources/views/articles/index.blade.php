@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Articles
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse">
                        </a>
                        <a href="#portlet-config" data-toggle="modal" class="config">
                        </a>
                        <a href="javascript:;" class="reload">
                        </a>
                        <a href="javascript:;" class="remove">
                        </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        Title
                                    </th>
                                    <th>
                                        Body
                                    </th>
                                    <th>
                                        Published On
                                    </th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($articles as $article)
                            <tr>
                                <td>
                                    <a href="{{ url('/articles', $article->id )}}">{{ $article->title }}</a>
                                </td>
                                <td>
                                    {{ str_limit($article->body, 50) }}
                                </td>
                                <td>
                                    {{ $article->published_at }}
                                </td>
                                <td>
                                    <a class="label label-danger" href="{{ url('/articles/'.$article->id.'/edit' )}}">Edit</a>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination"> {{ $articles->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
