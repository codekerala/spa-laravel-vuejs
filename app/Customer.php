<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\FilterPaginateOrder;

class Customer extends Model
{
    use FilterPaginateOrder;

    protected $fillable = [
        'company', 'email', 'name', 'phone', 'address'
    ];

    // whitelist of filter-able columns
    protected $filter = [
        'id', 'company', 'email', 'name', 'phone', 'address', 'created_at'
    ];

    public static function initialize()
    {
        return [
            'company' => '', 'email' => '', 'name' => '', 'phone' => '', 'address' => ''
        ];
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
