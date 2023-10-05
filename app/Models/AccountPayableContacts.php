<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountPayableContacts extends Model
{
    use HasFactory;

    protected $table = 'account_payable_contact';

    protected $fillable = [
        'account_id',
        'contact_id',
    ];
}
