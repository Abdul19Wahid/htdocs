<?php
namespace App\Http\Controllers;
use App\Models\ActivityLog; use Illuminate\Http\Request;
class ActivityLogController extends Controller {
 public function index(Request $r){$logs=$r->user()->activityLogs()->latest()->paginate(30);return view('activity_logs.index',compact('logs'));}
 public static function record(Request $r,string $action,string $module,?int $recordId,string $description):void{ActivityLog::create(['user_id'=>$r->user()->id,'action'=>$action,'module'=>$module,'record_id'=>$recordId,'description'=>$description,'ip_address'=>$r->ip(),'user_agent'=>substr((string)$r->userAgent(),0,1000)]);}
}
