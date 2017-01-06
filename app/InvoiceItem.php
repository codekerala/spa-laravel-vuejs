<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'description', 'qty', 'unit_price'
    ];

    public static function initialize()
    {
        return [
            'description' => '',
            'unit_price' => '',
            'qty' => ''
        ];
    }
}
