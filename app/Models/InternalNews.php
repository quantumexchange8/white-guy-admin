<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InternalNews extends Model
{
    use HasFactory;

    protected $table = "core_internalnews";

    public $timestamps = false;

    protected $fillable = [
        'note', 
        'file',
        'edited_at',
        'created_at',
    ];
}
