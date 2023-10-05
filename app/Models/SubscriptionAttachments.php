<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionAttachments extends Model
{
    use HasFactory;

    protected $table = 'subscription_attachment';

    protected $fillable = [
        'subscription_id',
        'name',
        'attachment_file',
        'attachment_link',
        'created_by',
        'updated_by',
    ];
}
