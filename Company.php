<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Company extends Model
{
    use HasFactory,HasRoles;

    protected $guard_name = 'web';

    protected $fillable = [
        'company_name',
        'company_phoneno',
        'company_address',
        'company_email',
    ];
}
