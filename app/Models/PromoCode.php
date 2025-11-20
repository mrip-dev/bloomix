<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $guarded = [];
     public function isValid()
    {
        $now = now();
        $validUsage = $this->usage_limit === null || $this->used_count < $this->usage_limit;
        $validDate  = (!$this->start_date || $now->gte($this->start_date)) &&
                      (!$this->end_date || $now->lte($this->end_date));

        return $validUsage && $validDate;
    }
}
