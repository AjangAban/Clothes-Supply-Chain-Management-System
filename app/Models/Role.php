<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * Get the users that belong to this role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the dashboard route for this role.
     */
    public function getDashboardRoute()
    {
        $routes = [
            'admin' => '/admin/dashboard',
            'vendor' => '/vendor/dashboard',
            'manufacturer' => '/manufacturer/dashboard',
            'retailer' => '/retailer/dashboard',
            'delivery' => '/delivery/dashboard',
            'raw_material_supplier' => '/supplier/dashboard',
            'supplier' => '/supplier/dashboard',
        ];
        return $routes[$this->name] ?? '/dashboard';
    }

    /**
     * Check if role can access a specific module.
     */
    public function canAccessModule($module)
    {
        $permissions = [
            'vendor' => ['vendor_management', 'chat', 'reports'],
            'manufacturer' => ['inventory_management', 'vendor_management', 'chat', 'analytics', 'reports'],
            'retailer' => ['inventory_management', 'order_management', 'customer_management', 'chat', 'analytics', 'reports'],
        ];
        return in_array($module, $permissions[$this->name] ?? []);
    }
}
