<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'pdf_path',
        'status',
        'validation_notes',
        'validation_results',
        'validated_at',
    ];

    /**
     * Get the vendor that owns the application.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
