<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class SettingController extends Controller { public function edit(Request $r){return view('settings.edit',['user'=>$r->user()]);} public function update(Request $r){$d=$r->validate(['currency'=>'required|string|size:3','language'=>'required|string|max:10','theme'=>'required|in:light,dark']);$r->user()->update($d);return back()->with('success','Settings saved.');} }
