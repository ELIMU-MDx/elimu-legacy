<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Experiment extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
        'requested_at',
        'processed_at'
    ];

    public function assay()
    {
        return $this->belongsTo(Assay::class);
    }

    public function processingLog()
    {
        return $this->belongsTo(ProcessingLog::class);
    }

    public function samples()
    {
        return $this->belongsToMany(Sample::class)->withPivot('status');
    }

    public function getStatusAttribute()
    {
        return optional($this->pivot)->status;
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class);
    }
}