<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepositRequestResource;
use App\Models\DepositRequest;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerDepositRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $depositRequests = DepositRequest::whereHasMorph('owner', [User::class], function ($query) use ($user) {
            $query->where('id', $user->id);
        })->latest()->get();

        return DepositRequestResource::collection($depositRequests);
    }

    public function details(DepositRequest $depositRequest)
    {
        return new DepositRequestResource($depositRequest);
    }

    public function create(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'amount' => 'required|numeric',
            'payment_channel' => 'required|string',
            'account_name' => 'required|string',
            'account_number' => 'required|string',
            'proof_img' => 'required|file|mimes:jpg,png,jpeg|max:2048',
        ]);

        $depositRequest = new DepositRequest();
        $depositRequest->amount = $validated['amount'];
        $depositRequest->payment_channel = $validated['payment_channel'];
        $depositRequest->account_name = $validated['account_name'];
        $depositRequest->account_number = $validated['account_number'];
        $depositRequest->owner()->associate($user);

        $file_name = time() . '_proof.' . $request->proof_img->getClientOriginalExtension();
        $file_path = $request->file('proof_img')->storeAs('deposit_proofs', $file_name, 'public');
        $depositRequest->proof_img_url = asset('storage/app/public/' . $file_path);

        $depositRequest->save();

        return new DepositRequestResource($depositRequest);
    }

    public function cancel(DepositRequest $depositRequest)
    {
        $depositRequest->cancel('Canceled by User');
        return new DepositRequestResource($depositRequest);
    }
}
