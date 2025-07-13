<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanCuti extends Model
{
    use HasFactory;

    protected $guarded = ['id']; 

    protected static function booted()
    {
        static::creating(function ($cuti) {
            if (auth()->check()) {
                $cuti->user_id = auth()->id();
            }
        });
    }

    // Relasi ke user pengaju
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke user yang menyetujui
    public function approver()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

}
