<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserClientChangelog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "users_clients_changelog";

    protected $casts = [
        'changes' => 'json',
    ];

    protected $fillable = [
        'users_clients_id',
        'column_name',
        'description',
    ];
}
