<?php

namespace App\Http\Controllers;

use App\Models\KycProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KycController extends Controller
{
    public function edit()
    {
        $kyc = KycProfile::query()->firstOrCreate(['user_id' => Auth::id()]);

        return view('kyc.edit', compact('kyc'));
    }

    public function update(Request $request)
    {
        $kyc = KycProfile::query()->firstOrCreate(['user_id' => Auth::id()]);

        if (in_array($kyc->status, ['pending', 'approved'], true)) {
            return back()->withErrors(['kyc' => 'e-KYC is under review and cannot be edited.']);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:120',
            'last_name' => 'required|string|max:120',
            'document_number' => 'required|string|max:80',
            'document_file' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:51200',
            'address' => 'required|string|max:255',
            'contact_number' => 'required|string|max:30',
            'bank_account_file' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:51200',
            'utility_bill_file' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:51200',
            'face_straight' => 'nullable|file|mimes:png,jpg,jpeg|max:51200',
            'face_left' => 'nullable|file|mimes:png,jpg,jpeg|max:51200',
            'face_right' => 'nullable|file|mimes:png,jpg,jpeg|max:51200',
            'face_top' => 'nullable|file|mimes:png,jpg,jpeg|max:51200',
            'face_bottom' => 'nullable|file|mimes:png,jpg,jpeg|max:51200',
        ]);

        $baseDir = 'private/kyc/' . Auth::id();

        $diskName = config('kyc.disk');
        $disk = Storage::disk($diskName);
        $visibility = config('kyc.visibility', 'private');

        $paths = [];

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $path = $baseDir . '/document-' . Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
            $disk->putFileAs($baseDir, $file, basename($path), ['visibility' => $visibility]);
            $paths['document_file_path'] = $path;
        }

        if ($request->hasFile('bank_account_file')) {
            $file = $request->file('bank_account_file');
            $path = $baseDir . '/bank-account-' . Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
            $disk->putFileAs($baseDir, $file, basename($path), ['visibility' => $visibility]);
            $paths['bank_account_file_path'] = $path;
        }

        if ($request->hasFile('utility_bill_file')) {
            $file = $request->file('utility_bill_file');
            $path = $baseDir . '/utility-bill-' . Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
            $disk->putFileAs($baseDir, $file, basename($path), ['visibility' => $visibility]);
            $paths['utility_bill_file_path'] = $path;
        }

        foreach (['face_straight', 'face_left', 'face_right', 'face_top', 'face_bottom'] as $faceField) {
            if ($request->hasFile($faceField)) {
                $file = $request->file($faceField);
                $path = $baseDir . '/' . $faceField . '-' . Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
                $disk->putFileAs($baseDir, $file, basename($path), ['visibility' => $visibility]);
                $paths[$faceField . '_path'] = $path;
            }
        }

        $kyc->fill([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'document_number_enc' => $validated['document_number'],
            'address_enc' => $validated['address'],
            'contact_number' => $validated['contact_number'],
        ]);

        $kyc->fill($paths);
        $kyc->status = $kyc->status === 'rejected' ? 'draft' : ($kyc->status ?: 'draft');
        $kyc->save();

        return back()->with('success', 'e-KYC saved.');
    }

    public function submit(Request $request)
    {
        $this->update($request);

        $kyc = KycProfile::query()->firstOrCreate(['user_id' => Auth::id()]);
        $kyc->status = 'pending';
        $kyc->submitted_at = now();
        $kyc->reviewed_at = null;
        $kyc->reviewed_by = null;
        $kyc->rejection_reason = null;
        $kyc->save();

        return back()->with('success', 'e-KYC submitted and pending review.');
    }

    public function faceVerify(Request $request)
    {
        $request->validate([
            'face_straight' => 'required|file|mimes:png,jpg,jpeg|max:51200',
            // Simplified validation for this context
        ]);

        $kyc = KycProfile::query()->firstOrCreate(['user_id' => Auth::id()]);
        $baseDir = 'private/kyc/' . Auth::id();

        $diskName = config('kyc.disk');
        $disk = Storage::disk($diskName);
        $visibility = config('kyc.visibility', 'private');

        $paths = [];
        // Only saving straight face for verification demo
        if ($request->hasFile('face_straight')) {
            $file = $request->file('face_straight');
            $path = $baseDir . '/face_straight-verify-' . Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
            $disk->putFileAs($baseDir, $file, basename($path), ['visibility' => $visibility]);
            $paths['face_straight_path'] = $path;
        }

        $kyc->fill($paths);
        $kyc->save();

        return response()->json([
            'ok' => true,
            'status' => 'uploaded',
        ]);
    }

    public function registerFaceDescriptor(Request $request)
    {
        $request->validate([
            'descriptor' => 'required|string', // JSON string
        ]);

        $kyc = KycProfile::query()->firstOrCreate(['user_id' => Auth::id()]);
        $kyc->face_descriptor = $request->descriptor;
        $kyc->save();

        return response()->json(['success' => true]);
    }

    public function download(string $type)
    {
        $kyc = KycProfile::query()->where('user_id', Auth::id())->firstOrFail();

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

    public function view(string $type)
    {
        $kyc = KycProfile::query()->where('user_id', Auth::id())->firstOrFail();

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

        return $disk->response($path);
    }

    public function faceRecognitionPage()
    {
        // Optimization: Select ONLY the face_descriptor to avoid loading heavy columns
        $kyc = KycProfile::query()
            ->select('face_descriptor')
            ->where('user_id', Auth::id())
            ->first();

        return view('kyc.face-recognition', [
            'face_descriptor' => $kyc ? $kyc->face_descriptor : null,
            'user_name' => Auth::user()->name
        ]);
    }
}
