<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'skill', 'shift', 'status'
    ];

    public function assignments()
    {
        return $this->hasMany(WorkerAssignment::class);
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function supplyCenter()
    {
        return $this->belongsTo(SupplyCenter::class);
    }
}
