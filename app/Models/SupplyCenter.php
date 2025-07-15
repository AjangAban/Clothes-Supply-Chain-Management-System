<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyCenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'location', 'manufacturer_id'
    ];

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function workers()
    {
        return $this->hasMany(Worker::class);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }
}
