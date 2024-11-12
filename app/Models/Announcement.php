<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Announcement extends Model
{
    use HasFactory;

    protected $table = 'core_announcement';

    public $timestamps = false;

    protected $fillable = [
        'title',
        'content',
        'edited_at',
        'created_at',
    ];
    
    /**
     * Site Model
     * Get the sites that belong to the announcement.
     */
    public function sites(): BelongsToMany
    {
        return $this->belongsToMany(Site::class, 'core_siteannouncement');
    }
}
