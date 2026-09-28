<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Concerns\OwnsRecords; use App\Models\SavingsGoal; use Illuminate\Http\Request;
class SavingsGoalController extends Controller { use OwnsRecords;
 public function index(Request $r){$items=$r->user()->savingsGoals()->latest()->paginate(15);return view('savings_goals.index',compact('items'));}
 public function create(){return view('savings_goals.create',['goal'=>new SavingsGoal(['status'=>'active'])]);}
 public function store(Request $r){$d=$this->validated($r);$d['user_id']=$r->user()->id;SavingsGoal::create($d);return to_route('savings-goals.index')->with('success','Savings goal created.');}
 public function show(Request $r,SavingsGoal $savings_goal){$this->authorizeOwner($r,$savings_goal);return view('savings_goals.show',['goal'=>$savings_goal]);}
 public function edit(Request $r,SavingsGoal $savings_goal){$this->authorizeOwner($r,$savings_goal);return view('savings_goals.edit',['goal'=>$savings_goal]);}
 public function update(Request $r,SavingsGoal $savings_goal){$this->authorizeOwner($r,$savings_goal);$d=$this->validated($r);if((float)$d['current_amount'] >= (float)$d['target_amount'])$d['status']='completed';$savings_goal->update($d);return to_route('savings-goals.index')->with('success','Savings goal updated.');}
 public function destroy(Request $r,SavingsGoal $savings_goal){$this->authorizeOwner($r,$savings_goal);$savings_goal->delete();return back()->with('success','Savings goal deleted.');}

 /**
  * Add (or withdraw, via a negative amount) money against a goal without
  * having to resubmit the whole edit form. This was the real gap — the
  * only way to log a contribution before was to open Edit and retype
  * name/target/date/description just to bump one number.
  */
 public function contribute(Request $r,SavingsGoal $savings_goal){
    $this->authorizeOwner($r,$savings_goal);
    $d=$r->validate(['amount'=>'required|numeric|not_in:0']);
    $newAmount=max(0,(float)$savings_goal->current_amount+(float)$d['amount']);
    $savings_goal->current_amount=$newAmount;
    if($newAmount>=(float)$savings_goal->target_amount && $savings_goal->status==='active'){
        $savings_goal->status='completed';
    } elseif($newAmount<(float)$savings_goal->target_amount && $savings_goal->status==='completed'){
        $savings_goal->status='active';
    }
    $savings_goal->save();
    $verb=$d['amount']>0?'Added':'Withdrew';
    $amountAbs=number_format(abs($d['amount']),2);
    if($r->wantsJson())return response()->json(['success'=>true,'current_amount'=>$newAmount,'progress_percentage'=>$savings_goal->progress_percentage,'status'=>$savings_goal->status,'message'=>"{$verb} {$amountAbs}."]);
    return back()->with('success',"{$verb} {$r->user()->currency} {$amountAbs} ".($d['amount']>0?'to':'from')." \"{$savings_goal->name}\".");
 }

 private function validated(Request $r):array{return $r->validate(['name'=>'required|string|max:120','target_amount'=>'required|numeric|min:0.01','current_amount'=>'required|numeric|min:0','target_date'=>'nullable|date','description'=>'nullable|string|max:1000','status'=>'required|in:active,completed,cancelled']);}
}
