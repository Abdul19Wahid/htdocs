<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Concerns\OwnsRecords; use App\Models\Notification; use Illuminate\Http\Request;
class NotificationController extends Controller { use OwnsRecords;
 public function index(Request $r){$items=$r->user()->financialNotifications()->latest()->paginate(20);return view('notifications.index',compact('items'));}
 public function read(Request $r,Notification $notification){$this->authorizeOwner($r,$notification);$notification->markAsRead();return back();}
 public function readAll(Request $r){$r->user()->financialNotifications()->where('is_read',false)->update(['is_read'=>true,'read_at'=>now()]);return back()->with('success','All notifications marked as read.');}
}
