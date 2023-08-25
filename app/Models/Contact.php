<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = "contacts";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(Contact::class, "user_id", "id");
    }

    public function addresses(): HasMany {
        return $this->hasMany(Address::class, 'contact_id', 'id');
    }
}
