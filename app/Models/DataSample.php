<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSample extends Model
{
    protected $table = 'data_sample';

    public function data() {
        return $this->belongsTo(Data::class);
    }

    public function getDataAttribute($value) {
        if($value) {
            return number_format((float) $value, 2, '.', '\'');
        }
        return 'Non Ampl.';
    }

    public function sample() {
        return $this->belongsTo(Sample::class);
    }

    public function getAdditionalAttribute($value) {
        return unserialize($value);
    }
    public function setAdditionalAttribute($value) {
        return serialize($value);
    }
}
