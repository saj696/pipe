<?php

namespace App\Http\Controllers;


use App\Http\Requests;
use App\Article;
use App\Tag;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class ArticlesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $articles = Article::latest()->paginate(5);
        return view('articles.index', compact('articles'));
    }

    public function show($id)
    {
        $article = Article::findOrFail($id);
        return view('articles.show', compact('article'));
    }

    public function create()
    {
        $tags = Tag::lists('name', 'id');
        return view('articles.create', compact('tags'));
    }

    public function store(ArticleRequest $request)
    {
        $article = Auth::user()->articles()->create($request->all());

        $tagIds = $request->input('tag_list');
        $article->tags()->sync($tagIds);

        Session()->flash('flash_message', 'Article has been created!');
        return redirect('articles');
    }

    public function edit($id)
    {
        $tags = Tag::lists('name', 'id');
        $article = Article::findOrFail($id);

        return view('articles.edit', compact(['article', 'tags']));
    }

    public function update($id, ArticleRequest $request)
    {
        $article = Article::findOrFail($id);
        $article->update($request->all());

        $tagIds = $request->input('tag_list');
        $article->tags()->sync($tagIds);
        return redirect('articles');
    }
}
