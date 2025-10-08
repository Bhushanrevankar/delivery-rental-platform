<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\DepositRequest;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class DepositRequestController extends Controller
{
    public function list(Request $request)
    {
        $from_date = $request->input('from');
        $to_date = $request->input('to');
        $user_id = $request->input('customer_id');

        $query = DepositRequest::with('owner')
            ->whereHasMorph('owner', [User::class], function ($query) use ($user_id) {
                if ($user_id != null) $query->where('id', $user_id);
            });

        if ($from_date != null && $to_date != null) {
            $query = $query->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
        }

        $depositRequests = $query->latest()->get();

        return view('admin-views.customer.wallet.deposit-request.list', compact('depositRequests'));
    }

    public function view(Request $request, DepositRequest $depositRequest)
    {
        $depositRequest->load('owner');
        return view('admin-views.customer.wallet.deposit-request.details', compact('depositRequest'));
    }

    public function approve(DepositRequest $depositRequest)
    {
        $depositRequest->approve();

        if ($depositRequest->owner instanceof User) {
            $data = [
                'title' => 'Deposit Request Approved',
                'description' => 'Your deposit request approved by admin. Your wallet received additional funds.',
                'order_id' => '',
                'image' => '',
                'type' => 'general'
            ];

            Helpers::send_push_notif_to_device($depositRequest->owner->cm_firebase_token, $data);
            UserNotification::create([
                'data' => json_encode($data),
                'user_id' => $depositRequest->owner->id,
            ]);
        }

        return back();
    }

    public function reject(DepositRequest $depositRequest)
    {
        $depositRequest->reject('Rejected by Admin.');

        if ($depositRequest->owner instanceof User) {
            $data = [
                'title' => 'Deposit Request Rejected',
                'description' => 'Your deposit request rejected by admin. Please check details and try again.',
                'order_id' => '',
                'image' => '',
                'type' => 'general'
            ];

            Helpers::send_push_notif_to_device($depositRequest->owner->cm_firebase_token, $data);
            UserNotification::create([
                'data' => json_encode($data),
                'user_id' => $depositRequest->owner->id,
            ]);
        }

        return back();
    }

    public function delete(DepositRequest $depositRequest)
    {
        $depositRequest->delete();
        return back();
    }
}
