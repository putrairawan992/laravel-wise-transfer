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
        $enquiry->load(['replies.user']);
        return view('enquiries.show', compact('enquiry'));
    }

    public function reply(Request $request, Enquiry $enquiry)
    {
        if ($enquiry->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string',
        ]);

        $enquiry->replies()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        $enquiry->update(['status' => 'open']); // Re-open if user replies

        return back()->with('success', 'Reply sent successfully.');
    }
}
