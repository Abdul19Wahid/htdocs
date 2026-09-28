<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Concerns\OwnsRecords; use App\Models\Budget; use Illuminate\Http\Request; use Illuminate\Validation\Rule;
class BudgetController extends Controller { use OwnsRecords;
 public function index(Request $r){
    $items=$r->user()->budgets()->with('category')->latest('start_date')->paginate(15);
    $budgets=$items->getCollection();

    // Was one whereBetween()->sum() query PER budget on every page load
    // (visible, uncached — unlike the dashboard's version of this same
    // pattern, which is at least throttled). Fetch the relevant window's
    // expenses once and sum per-budget in PHP instead.
    if($budgets->isNotEmpty()){
        $earliestStart=$budgets->min('start_date');
        $latestEnd=$budgets->max('end_date');
        $expenses=$r->user()->expenses()->whereBetween('date',[$earliestStart,$latestEnd])->get(['category_id','date','amount']);
        $budgets->transform(function($b)use($expenses){
            $matching=$expenses->filter(fn($e)=>!($e->date->lt($b->start_date)||$e->date->gt($b->end_date))&&(!$b->category_id||$e->category_id===$b->category_id));
            $b->spent=(float)$matching->sum('amount');
            $b->percent=$b->amount>0?round($b->spent/$b->amount*100,1):0;
            return $b;
        });
    }

    return view('budgets.index',compact('items'));
 }
 public function create(Request $r){return view('budgets.create',['budget'=>new Budget(),'categories'=>$r->user()->categories()->orderBy('name')->get()]);}
 public function store(Request $r){$d=$this->validated($r);$d['user_id']=$r->user()->id;Budget::create($d);return to_route('budgets.index')->with('success','Budget created.');}
 public function show(Request $r,Budget $budget){$this->authorizeOwner($r,$budget);$spent=$r->user()->expenses()->when($budget->category_id,fn($q)=>$q->where('category_id',$budget->category_id))->whereBetween('date',[$budget->start_date,$budget->end_date])->sum('amount');return view('budgets.show',compact('budget','spent'));}
 public function edit(Request $r,Budget $budget){$this->authorizeOwner($r,$budget);return view('budgets.edit',compact('budget')+['categories'=>$r->user()->categories()->orderBy('name')->get()]);}
 public function update(Request $r,Budget $budget){$this->authorizeOwner($r,$budget);$budget->update($this->validated($r));return to_route('budgets.index')->with('success','Budget updated.');}
 public function destroy(Request $r,Budget $budget){$this->authorizeOwner($r,$budget);$budget->delete();return back()->with('success','Budget deleted.');}
 private function validated(Request $r):array{return $r->validate(['category_id'=>['nullable',Rule::exists('categories','id')->where(fn($q)=>$q->where('user_id',$r->user()->id))],'name'=>'required|string|max:120','amount'=>'required|numeric|min:0.01','start_date'=>'required|date','end_date'=>'required|date|after_or_equal:start_date','alert_percentage'=>'required|integer|min:1|max:100','is_recurring'=>'nullable|boolean']);}
}
