<?php

namespace Modules\Subscription\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Subscription\Entities\CreditDeductionRule;
use App\Models\Module;
use Brian2694\Toastr\Facades\Toastr;

class CreditDeductionRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $rules = CreditDeductionRule::with('module')->latest()->paginate(10);
        return view('subscription::admin.credit_rules.index', compact('rules'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $modules = Module::where('status', 1)->get();
        return view('subscription::admin.credit_rules.create', compact('modules'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_type' => 'required|string',
            'condition_type' => 'required|string',
            'credits_to_deduct' => 'required|numeric',
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'module_id' => 'nullable|integer',
            'status' => 'required|boolean',
        ]);

        CreditDeductionRule::create($request->all());

        return redirect()->route('admin.users.subscription.credit-rules.index')
            ->with('success', 'Rule created successfully.');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('subscription::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(CreditDeductionRule $credit_rule)
    {
        $modules = Module::where('status', 1)->get();
        return view('subscription::admin.credit_rules.edit', compact('credit_rule', 'modules'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, CreditDeductionRule $credit_rule)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_type' => 'required|string',
            'condition_type' => 'required|string',
            'credits_to_deduct' => 'required|numeric',
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'module_id' => 'nullable|integer',
            'status' => 'required|boolean',
        ]);

        $credit_rule->update($request->all());

        return redirect()->route('admin.users.subscription.credit-rules.index')
            ->with('success', 'Rule updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(CreditDeductionRule $credit_rule)
    {
        $credit_rule->delete();
        return redirect()->route('admin.users.subscription.credit-rules.index')
            ->with('success', 'Rule deleted successfully.');
    }

    public function status($id, $status)
    {
        $rule = CreditDeductionRule::find($id);
        $rule->status = $status;
        $rule->save();
        Toastr::success('Rule status updated!');
        return back();
    }
}
