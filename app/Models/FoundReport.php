<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoundReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'lost_item_id',
        'reporter_id',
        'message',
        'image_path',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function lostItem()
    {
        return $this->belongsTo(LostItem::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function notification()
    {
        return $this->hasOne(Notification::class);
    }
}
