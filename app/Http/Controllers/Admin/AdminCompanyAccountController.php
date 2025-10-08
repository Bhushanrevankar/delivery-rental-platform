<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyAccount;
use Illuminate\Http\Request;

class AdminCompanyAccountController extends Controller
{
    public function list()
    {
        $companyAccounts = CompanyAccount::all();
        return view('admin-views.business-settings.company-accounts.list', compact('companyAccounts'));
    }

    public function create()
    {
        return view('admin-views.business-settings.company-accounts.create');
    }

    public function edit(Request $request, CompanyAccount $companyAccount)
    {
        return view('admin-views.business-settings.company-accounts.edit', compact('companyAccount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nickname' => 'required|string',
            'bank_name' => 'required|string',
            'account_name' => 'required|string',
            'account_number' => 'required|string',
            'qr_img' => 'mimes:jpg,png,jpeg|max:2048',
        ]);

        $companyAccount = new CompanyAccount($validated);

        if ($request->file('qr_img')) {
            $file_name = time() . '_' . $request->qr_img->getClientOriginalName();
            $file_path = $request->file('qr_img')->storeAs('company_account_qr_codes', $file_name, 'public');
            // $companyAccount->qr_img_url = 'storage/app/public/' . $file_path;
            $companyAccount->qr_img_url = asset('storage/app/public/' . $file_path);
        }

        $companyAccount->save();

        return redirect(route('admin.business-settings.company-accounts'));
    }

    public function update(Request $request, CompanyAccount $companyAccount)
    {
        $validated = $request->validate([
            'nickname' => 'required|string',
            'bank_name' => 'required|string',
            'account_name' => 'required|string',
            'account_number' => 'required|string',
            'qr_img' => 'mimes:jpg,png,jpeg|max:2048',
        ]);

        $companyAccount->fill($validated);

        if ($request->file('qr_img')) {
            $file_name = time() . '_' . $request->qr_img->getClientOriginalName();
            $file_path = $request->file('qr_img')->storeAs('company_account_qr_codes', $file_name, 'public');
            // $companyAccount->qr_img_url = 'storage/app/public/' . $file_path;
            $companyAccount->qr_img_url = asset('storage/app/public/' . $file_path);
        }

        $companyAccount->save();

        return redirect(route('admin.business-settings.company-accounts'));
    }

    public function status(CompanyAccount $companyAccount)
    {
        $currentStatus = $companyAccount->availability;

        if ($currentStatus) {
            $companyAccount->availability = false;
        } else {
            $companyAccount->availability = true;
        }

        $companyAccount->save();

        return back();
    }

    public function delete(CompanyAccount $companyAccount)
    {
        $companyAccount->delete();
        return back();
    }
}
