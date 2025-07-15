<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialSupplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'company_name',
        'contact_person',
        'email',
        'phone',
        'address',
        'status',
        'specializations',
        'certifications',
    ];

    protected $casts = [
        'specializations' => 'array',
        'certifications' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'supplier_id');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'supplier_id');
    }

    /**
     * Get the purchase orders from manufacturers.
     */
    public function purchaseOrders()
    {
        return $this->hasMany(Order::class, 'supplier_id');
    }

    /**
     * Get the manufacturers who ordered from this supplier.
     */
    /*
     public function manufacturers()
    {
        return $this->belongsToMany(Manufacturer::class, 'orders', 'supplier_id', 'manufacturer_id')->distinct();
    }

    /**
     * Get the conversations with manufacturers.
     */
    public function conversationsWithManufacturers()
    {
        return $this->hasMany(\App\Models\Conversation::class, 'supplier_id');
    }

    public function manufacturers()
    {
        return $this->belongsToMany(Manufacturer::class, 'manufacturer_supplier', 'supplier_id', 'manufacturer_id');
    }


}
