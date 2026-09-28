<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Concerns\OwnsRecords; use App\Models\IncomeSource; use Illuminate\Http\Request; use Illuminate\Validation\Rule;
class IncomeSourceController extends Controller { use OwnsRecords;
 public function index(Request $r){$items=$r->user()->incomeSources()->withCount('incomes')->orderBy('name')->paginate(20);return view('income_sources.index',compact('items'));}
 public function create(){return view('income_sources.create',['item'=>new IncomeSource()]);}
 public function store(Request $r){$d=$r->validate(['name'=>['required','string','max:100',Rule::unique('income_sources')->where(fn($q)=>$q->where('user_id',$r->user()->id))],'description'=>'nullable|string|max:500']);$d['user_id']=$r->user()->id;IncomeSource::create($d);return to_route('income-sources.index')->with('success','Income source created.');}
 public function show(Request $r,IncomeSource $income_source){$this->authorizeOwner($r,$income_source);return view('income_sources.show',['item'=>$income_source]);}
 public function edit(Request $r,IncomeSource $income_source){$this->authorizeOwner($r,$income_source);return view('income_sources.edit',['item'=>$income_source]);}
 public function update(Request $r,IncomeSource $income_source){$this->authorizeOwner($r,$income_source);$d=$r->validate(['name'=>['required','string','max:100',Rule::unique('income_sources')->ignore($income_source->id)->where(fn($q)=>$q->where('user_id',$r->user()->id))],'description'=>'nullable|string|max:500']);$income_source->update($d);return to_route('income-sources.index')->with('success','Income source updated.');}
 public function destroy(Request $r,IncomeSource $income_source){$this->authorizeOwner($r,$income_source);$income_source->delete();return back()->with('success','Income source deleted.');}
}
