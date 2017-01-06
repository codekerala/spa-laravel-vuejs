<?php

Route::get('/', function () {
    return view('app');
});

Route::group(['prefix' => 'api/'], function() {
    Route::resource('customer', 'CustomerController');
    Route::resource('invoice', 'InvoiceController');
});