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
        return view('admin.enquiries.show', compact('enquiry'));
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
