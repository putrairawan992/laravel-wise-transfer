<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnquiryReply extends Model
{
    protected $guarded = ['id'];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class); // Sender
    }
}
