<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'quantity', 'unit', 'manufacturer_id', 'reorder_point'
    ];

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class, 'item_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }
}
