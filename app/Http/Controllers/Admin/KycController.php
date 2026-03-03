<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycProfile;
use App\Notifications\KycStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    public function index()
    {
        $kycs = KycProfile::with('user')->latest()->paginate(20);

        return view('admin.kyc.index', compact('kycs'));
    }

    public function show(KycProfile $kyc)
    {
        $kyc->load('user', 'reviewer');

        return view('admin.kyc.show', compact('kyc'));
    }

    public function approve(KycProfile $kyc)
    {
        $kyc->status = 'approved';
        $kyc->reviewed_at = now();
        $kyc->reviewed_by = Auth::id();
        $kyc->rejection_reason = null;
        $kyc->save();

        $kyc->user?->notify(new KycStatusChanged($kyc));

        return back()->with('success', 'KYC approved.');
    }

    public function reject(Request $request, KycProfile $kyc)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:2000',
        ]);

        $kyc->status = 'rejected';
        $kyc->reviewed_at = now();
        $kyc->reviewed_by = Auth::id();
        $kyc->rejection_reason = $validated['rejection_reason'];
        $kyc->save();

        $kyc->user?->notify(new KycStatusChanged($kyc));

        return back()->with('success', 'KYC rejected.');
    }

    public function download(KycProfile $kyc, string $type)
    {
        $disk = Storage::disk(config('kyc.disk'));

        $map = [
            'document' => $kyc->document_file_path,
            'bank' => $kyc->bank_account_file_path,
            'utility' => $kyc->utility_bill_file_path,
            'face_straight' => $kyc->face_straight_path,
            'face_left' => $kyc->face_left_path,
            'face_right' => $kyc->face_right_path,
            'face_top' => $kyc->face_top_path,
            'face_bottom' => $kyc->face_bottom_path,
        ];

        $path = $map[$type] ?? null;
        if (!$path || !$disk->exists($path)) {
            abort(404);
        }

        return $disk->download($path);
    }
}
