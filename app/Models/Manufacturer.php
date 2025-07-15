<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RawMaterialSupplier;


class Manufacturer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'user_id',
        'status',
        'capacity',
        'description',
        'certifications',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'certifications' => 'array',
    ];

    /**
     * Get the user that owns the manufacturer.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the products for this manufacturer.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the production orders for this manufacturer.
     */
    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class);
    }

    /**
     * Get the inventories for this manufacturer.
     */
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    /**
     * Get the quality checks for this manufacturer.
     */
    public function qualityChecks()
    {
        return $this->hasMany(QualityCheck::class);
    }

    /**
     * Get the raw material suppliers for this manufacturer.
     */
    /*
     public function rawMaterialSuppliers()
    {
        return $this->hasMany(RawMaterialSupplier::class);
    }

    /**
     * Get the purchase orders to suppliers.
     */
    public function purchaseOrders()
    {
        return $this->hasMany(Order::class, 'manufacturer_id');
    }

    /**
     * Get the suppliers this manufacturer has ordered from.
     */
    public function suppliers()
    {
        return $this->belongsToMany(RawMaterialSupplier::class, 'orders', 'manufacturer_id', 'supplier_id')->distinct();
    }

    /**
     * Get the product offers sent to vendors/warehouses.
     */
    public function productOffers()
    {
        return $this->hasMany(Product::class, 'manufacturer_id');
    }

    /**
     * Get the conversations with suppliers/vendors/warehouses.
     */
    public function conversations()
    {
        return $this->hasMany(\App\Models\Conversation::class, 'manufacturer_id');
    }
    
    public function rawMaterialSuppliers()
    {
        return $this->belongsToMany(RawMaterialSupplier::class, 'manufacturer_supplier', 'manufacturer_id', 'supplier_id');
    }

} 