<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// 以下の1行を追記することで、Profile Modelが扱えるようになる
use App\Models\Profile;

class ProfileController extends Controller
{
    public function add()
    {
        return view('admin.profile.create');
    }
    
     public function create(Request $request)
    {
        // 以下を追記
        // Validationを行う
        $this->validate($request, News::$rules);

        $news = new News;
        $form = $request->all();
        
        // フォームから画像が送信されてきたら、保存して、$news->image_path に画像のパスを保存する
        if (isset($form['image'])) {
            $path = $request->file('image')->store('public/image');
            $news->image_path = basename($path);
        } else {
            $news->image_path = null;
        }
            
        // フォームから送信されてきた_tokenを削除する
        unset($form['_token']);
        // フォームから送信されてきたimageを削除する
        unset($form['image']);    
            
        // データベースに保存する
        $news->fill($form);
        $news->save();    
        
        return redirect('admin/profile/create');
    }
    
    // 以下を追記
    public function index(Request $request)
    {
        $cond_title = $request->cond_title;
        if ($cond_title != '') {
            // 検索されたら検索結果を取得する
            $posts = Profile::where('title', $cond_title)->get();
        } else {
            // それ以外はすべてのニュースを取得する
            $posts = Profile::all();
        }
        return view('admin.profile.index', ['posts' => $posts, 'cond_title' => $cond_title]);
    }
     
    // 以下を追記

    public function edit(Request $request)
    {
        // News Modelからデータを取得する
        $news = News::find($request->id);
        if (empty($news)) {
            abort(404);
        }
        return view('admin.profile.edit', ['profile_form' => $news]);
    }    
    
    public function update(Request $request)
    {
        // Validationをかける
        $this->validate($request, Profile::$rules);
　　　　// News Modelからデータを取得する
        $news = Profile::find($request->id);
        // 送信されてきたフォームデータを格納する
        $news_form = $request->all();
        
        unset($profile_form['_token']);

        // 該当するデータを上書きして保存する
        $news->fill($news_form)->save();

        return redirect('admin/profile');
    }
}
