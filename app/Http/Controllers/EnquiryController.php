<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function index()
    {
        $enquiries = Enquiry::where('user_id', auth()->id())->latest()->get();
        return view('enquiries.index', compact('enquiries'));
    }

    public function create()
    {
        return view('enquiries.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Enquiry::create([
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return redirect()->route('enquiries.index')->with('success', 'Enquiry submitted successfully.');
    }

    public function show(Enquiry $enquiry)
    {
        if ($enquiry->user_id !== auth()->id()) {
            abort(403);
        }
        return view('enquiries.show', compact('enquiry'));
    }
}
