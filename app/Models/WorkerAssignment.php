<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id', 'shift_id', 'status'
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
