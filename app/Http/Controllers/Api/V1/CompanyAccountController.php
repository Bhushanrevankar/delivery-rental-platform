<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyAccountResource;
use App\Models\CompanyAccount;

class CompanyAccountController extends Controller
{
    public function index()
    {
        $companyAccounts = CompanyAccount::where('availability', true)->get();
        return CompanyAccountResource::collection($companyAccounts);
    }
}
