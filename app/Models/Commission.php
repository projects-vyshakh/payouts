<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'sale_id', 'level', 'amount'];

    public function user() {

        return $this->belongsTo(User::class);
    }

    public function sale() {
        return $this->hasMany(Sale::class);
    }
}
