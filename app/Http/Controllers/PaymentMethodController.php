<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Concerns\OwnsRecords; use App\Models\PaymentMethod; use Illuminate\Http\Request; use Illuminate\Validation\Rule;
class PaymentMethodController extends Controller { use OwnsRecords;
 public function index(Request $r){$items=$r->user()->paymentMethods()->withCount(['incomes','expenses'])->orderByDesc('is_default')->orderBy('name')->paginate(20);return view('payment_methods.index',compact('items'));}
 public function create(){return view('payment_methods.create',['item'=>new PaymentMethod()]);}
 public function store(Request $r){$d=$r->validate(['name'=>['required','string','max:80',Rule::unique('payment_methods')->where(fn($q)=>$q->where('user_id',$r->user()->id))],'icon'=>'nullable|string|max:50','is_default'=>'nullable|boolean']);$d['user_id']=$r->user()->id;$d['is_default']=(bool)($d['is_default']??false);if($d['is_default'])$r->user()->paymentMethods()->update(['is_default'=>false]);PaymentMethod::create($d);return to_route('payment-methods.index')->with('success','Payment method created.');}
 public function show(Request $r,PaymentMethod $payment_method){$this->authorizeOwner($r,$payment_method);return view('payment_methods.show',['item'=>$payment_method]);}
 public function edit(Request $r,PaymentMethod $payment_method){$this->authorizeOwner($r,$payment_method);return view('payment_methods.edit',['item'=>$payment_method]);}
 public function update(Request $r,PaymentMethod $payment_method){$this->authorizeOwner($r,$payment_method);$d=$r->validate(['name'=>['required','string','max:80',Rule::unique('payment_methods')->ignore($payment_method->id)->where(fn($q)=>$q->where('user_id',$r->user()->id))],'icon'=>'nullable|string|max:50','is_default'=>'nullable|boolean']);$d['is_default']=(bool)($d['is_default']??false);if($d['is_default'])$r->user()->paymentMethods()->where('id','!=',$payment_method->id)->update(['is_default'=>false]);$payment_method->update($d);return to_route('payment-methods.index')->with('success','Payment method updated.');}
 public function destroy(Request $r,PaymentMethod $payment_method){$this->authorizeOwner($r,$payment_method);$payment_method->delete();return back()->with('success','Payment method deleted.');}
}
