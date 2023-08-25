<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;

class User extends Model implements Authenticatable
{
    protected $table = "users";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
       'username',
       'password',
       'name' 
    ];

    public function contacts(): HasMany {
        return $this->hasMany(Contact::class, "user_id", "id");
    }

    public function getAuthIdentifierName()
    {
        return 'username';
    }

    public function getAuthIdentifier()
    {
        return $this->username;
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        // data masih dummy, implementasi sebenarnya berbeda dengan ini
        return $this->token;
    }

    public function setRememberToken($value)
    {
        // data masih dummy, implementasi sebenarnya berbeda dengan ini
        $this->token = $value;
    }

    public function getRememberTokenName()
    {
        // data masih dummy, implementasi sebenarnya berbeda dengan ini
        return 'token';
    }

}
