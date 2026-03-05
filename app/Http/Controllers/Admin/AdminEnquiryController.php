<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;

class AdminEnquiryController extends Controller
{
    public function index()
    {
        $enquiries = Enquiry::with('user')->latest()->paginate(10);
        return view('admin.enquiries.index', compact('enquiries'));
    }

    public function show(Enquiry $enquiry)
    {
        $enquiry->load(['user', 'replies.user']);
        return view('admin.enquiries.show', compact('enquiry'));
    }

    public function reply(Request $request, Enquiry $enquiry)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $enquiry->replies()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        // Auto update status if needed
        if ($enquiry->status === 'open') {
            $enquiry->update(['status' => 'pending']); // Pending user response
        }

        return back()->with('success', 'Reply sent successfully.');
    }

    public function updateStatus(Request $request, Enquiry $enquiry)
    {
        $request->validate([
            'status' => 'required|in:open,closed,pending',
        ]);

        $enquiry->update(['status' => $request->status]);

        return back()->with('success', 'Ticket status updated successfully.');
    }
}
