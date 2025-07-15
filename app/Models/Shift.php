<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'center', 'date', 'start_time', 'end_time', 'required_workers', 'manufacturer_id', 'supply_center_id'
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
