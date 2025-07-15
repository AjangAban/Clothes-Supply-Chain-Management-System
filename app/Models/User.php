<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'address',
        'company_name',
        'position',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the vendor associated with the user.
     */
    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    /**
     * Get the delivery partner associated with the user.
     */
    public function deliveryPartner()
    {
        return $this->hasOne(DeliveryPartner::class);
    }

    /**
     * Get the warehouse managed by the user.
     */
    public function managedWarehouse()
    {
        return $this->hasOne(Warehouse::class, 'manager_id');
    }

    /**
     * Get the retail store managed by the user.
     */
    public function managedRetailStore()
    {
        return $this->hasOne(RetailStore::class, 'manager_id');
    }

    /**
     * Get the orders placed by the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the deliveries assigned to the user.
     */
    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'driver_id');
    }

    /**
     * Get the conversations where user is sender.
     */
    /*
     public function sentConversations()
    {
        return $this->hasMany(Conversation::class, 'sender_id');
    }

    /**
     * Get the conversations where user is receiver.
     */
    /*
     public function receivedConversations()
    {
        return $this->hasMany(Conversation::class, 'receiver_id');
    }

    /**
     * Get the messages sent by the user.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get the report logs for the user.
     */
    public function reportLogs()
    {
        return $this->hasMany(ReportLog::class);
    }

    /**
     * Get the user report preferences.
     */
    public function userReports()
    {
        return $this->hasMany(UserReport::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole($role)
    {
        return $this->role && $this->role->name === $role;
    }

    /**
     * Check if user has any of the specified roles.
     */
    public function hasAnyRole($roles)
    {
        if (!$this->role) return false;
        
        if (is_array($roles)) {
            return in_array($this->role->name, $roles);
        }
        
        return $this->role->name === $roles;
    }

    /**
     * Get all notifications for the user.
     */
    public function notifications()
    {
        return $this->morphMany(\Illuminate\Notifications\DatabaseNotification::class, 'notifiable')->orderBy('created_at', 'desc');
    }

    /**
     * Get the production orders for the manufacturer.
     */
    public function productionOrders()
    {
        return $this->hasMany(\App\Models\ProductionOrder::class, 'manufacturer_id');
    }

    /**
     * Get the inventories managed by the user (for warehouse manager, retailer, manufacturer).
     */
    public function inventories()
    {
        // This assumes inventories table has a warehouse_id or retail_store_id or manufacturer_id
        // You may need to adjust this logic based on your actual schema
        return $this->hasMany(\App\Models\Inventory::class, 'manager_id');
    }

    /**
     * Get the vendor applications for the vendor.
     */
    public function vendorApplications()
    {
        return $this->hasMany(\App\Models\VendorApplication::class, 'vendor_id');
    }

    /**
     * Get the facility visits for the vendor.
     */
    public function facilityVisits()
    {
        return $this->hasMany(\App\Models\FacilityVisit::class, 'vendor_id');
    }

    /**
     * Get the raw material supplies for the supplier.
     */
    public function rawMaterialSupplies()
    {
        return $this->hasMany(\App\Models\RawMaterialSupplier::class, 'user_id');
    }

    /**
     * Get the orders for the retail store managed by the user.
     */
    public function retailStoreOrders()
    {
        return $this->hasManyThrough(
            \App\Models\Order::class,
            \App\Models\RetailStore::class,
            'manager_id', // Foreign key on retail_stores table...
            'retail_store_id', // Foreign key on orders table...
            'id', // Local key on users table...
            'id' // Local key on retail_stores table...
        );
    }

    /**
     * Get the manufacturer profile associated with the user.
     */
    
     //public function manufacturer()
    //{
    //    return $this->belongsTo(Manufacturer::class);
    //}

    public function manufacturer()
    {
    return $this->hasOne(\App\Models\Manufacturer::class, 'user_id');
    }


    /**
     * Get the raw material supplier associated with the user.
     */
    public function rawMaterialSupplier()
    {
        return $this->hasOne(\App\Models\RawMaterialSupplier::class, 'user_id');
    }

    /**
     * Get the orders where this user is the retailer.
     */
    public function retailerOrders()
    {
        return $this->hasMany(Order::class, 'retailer_id');
    }

    /**
     * Get the conversations for this user (all roles).
     */
    
    // public function conversations()
    //{
   //     return $this->belongsToMany(Conversation::class, 'conversation_user');
    //}
}
