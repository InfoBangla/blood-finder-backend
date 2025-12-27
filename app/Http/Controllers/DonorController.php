<?php

namespace App\Http\Controllers;

use App\Http\Resources\DonorResource;
use App\Models\AccessLog;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Crypt;

class DonorController extends Controller
{
    public function index(Request $request)
    {
        $donors = User::where('type', 'donor')
            ->get([
                'id',
                'name',
                'phone',
                'area_id',
                'blood_group',
                'last_donation_date'
            ]);

        return [
            'status' => 'success',
            'code' => 200,
            'message' => '',
            'data' => DonorResource::collection($donors)
        ];
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|numeric|unique:users|digits:11',
            'bloodGroup' => 'required|string',
            'area' => 'required|numeric',
            'lastDonationDate' => 'nullable|date',
        ]);

        $donor = User::create([
            'type' => 'donor',
            'name' => $request->name,
            'phone' => $request->phone,
            'phone_hash' => $request->phone,
            // 'phone_verified_at' => now(),
            'blood_group' => $request->bloodGroup,
            'area_id' => $request->area,
            'last_donation_date' => $request->lastDonationDate ?? now()->subYear(5),
            'password' => 'P@$$word'
        ]);

        return [
            'status' => 'success',
            'code' => 200,
            'message' => 'Donor registered successfully',
            'data' => new DonorResource($donor)
        ];
    }

    public function search(Request $request)
    {
        $request->validate([
            'bloodGroup' => 'required|string',
            'area' => 'required|numeric',
        ]);

        $donors = User::where('blood_group', $request->bloodGroup)
            ->where('area_id', $request->area)
            ->whereNotNull('phone_verified_at')
            ->where('is_blocked', false)
            ->whereDate('last_donation_date', '<=', now()->subDays(90))
            ->get([
                'id',
                'name',
                'phone',
                'area_id',
                'blood_group',
                'last_donation_date'
            ]);

        return [
            'status' => 'success',
            'code' => 200,
            'message' => '',
            'data' => DonorResource::collection($donors)
        ];
    }

    public function phone(Request $request)
    {
        $request->validate([
            'hash' => 'required',
        ]);

        try {
            $id = Crypt::decryptString($request->hash);
        } catch (Exception $ex) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => 'The payload is invalid.',
                'data' => [],
                'errors' => []
            ];
        }

        AccessLog::create([
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'action' => 'DonorController::phone',
            'payload' => ['donor_id' => $id]
        ]);

        $donor = User::whereId($id)->first(['phone as original_phone']);

        return [
            'status' => 'success',
            'code' => 200,
            'message' => '',
            'data' => [
                'phone' => Crypt::decryptString($donor->original_phone)
            ]
        ];
    }

    public function serviceRequest(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'type' => 'required',
            'payload' => 'nullable',
            'note' => 'nullable|max:250'
        ]);

        $hash = hash('sha256', $request->phone);
        $user = User::where('phone_hash', $hash)->first(['id']);

        if (!$user) {
            return [
                'status' => 'error',
                'code' => 400,
                'message' => 'কোনো রক্তদাতার তথ্য পাওয়া যায় নাই।',
                'data' => [],
                'errors' => [],
            ];
        }

        $serviceRequestExists = ServiceRequest::whereUserId($user->id)->where('type', $request->type)->exists();

        if ($serviceRequestExists) {
            return [
                'status' => 'success',
                'code' => 200,
                'message' => 'আপনার অনুরোধটি গ্রহণ করা হয়েছে। যাচাই করার পর তথ্য পরিবর্তন বা মুছে ফেলা হবে।',
                'data' => [],
                'errors' => [],
            ];
        }

        $user = ServiceRequest::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'payload' => $request->payload,
            'note' => $request->note,
        ]);

        return [
            'status' => 'success',
            'code' => 200,
            'message' => 'আপনার অনুরোধটি গ্রহণ করা হয়েছে। যাচাই করার পর তথ্য পরিবর্তন বা মুছে ফেলা হবে।',
            'data' => [],
            'errors' => [],
        ];
    }
}
