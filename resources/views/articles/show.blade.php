@extends('layouts.app')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Article Detail
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
                                    Tags
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        {{ $article->title }}
                                    </td>
                                    <td>
                                        <p>{{ $article->body }}</p>
                                    </td>
                                    <td>
                                        {{ $article->published_at }}
                                    </td>
                                    <td>
                                        @unless($article->tags->isEmpty())
                                            @foreach($article->tags as $tag)
                                                <p>{{ $tag->name }}</p>
                                            @endforeach
                                        @endunless
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END BORDERED TABLE PORTLET-->
        </div>
    </div>
@stop