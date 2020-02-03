<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Profile;
use App\Pistory;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function add()
    {
        return view('admin.profile.create');
    }
    
    public function create(Request $request)
    {
      $this->validate($request, Profile::$rules);

      $profiles = new Profile;
      $form = $request->all();
      
      $profiles->fill($form);
      $profiles->save();
        
      return redirect('admin/profile/create');
    }
    
   public function index(Request $request)
  {
      $cond_title = $request->cond_title;
      if ($cond_title != '') {
          // 検索されたら検索結果を取得する
          $posts = Profile::where('name', $cond_title)->get();
      } else {
          // それ以外はすべてのニュースを取得する
          $posts = Profile::all();
      }
      return view('admin.profile.index', ['posts' => $posts, 'cond_title' => $cond_title]);
  }
  
  public function edit(Request $request)
  {
      // Profile Modelからデータを取得する
      $profiles = Profile::find($request->id);
      if (empty($profiles)) {
        abort(404);    
      }
      return view('admin.profile.edit', ['profiles_form' => $profiles]);
  }


  public function update(Request $request)
  {
      // Validationをかける
      $this->validate($request, Profile::$rules);
      // Profile Modelからデータを取得する
      $profiles = Profile::find($request->id);
      // 送信されてきたフォームデータを格納する
      $profiles_form = $request->all();
      unset($profiles_form['_token']);
      $profiles->fill($profiles_form)->save();

      // 該当するデータを上書きして保存する
      
      $pistory = new Pistory;
      $pistory ->profile_id = $profiles->id;
      $pistory ->edited_at = Carbon::now();
      $pistory ->save();
      
      return redirect('admin/profile/');
  }
  
  public function delete(Request $request)
  {
      // 該当する Profile Modelを取得
      $profiles = Profile::find($request->id);
      // 削除する
      $profiles->delete();
      return redirect('admin/profile/');
  }  


}
