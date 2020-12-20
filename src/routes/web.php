<?php


//Route::get('payzone', function () {
//    return view('payzone::index');
//});

Route::namespace('Svodya\PayZone\Http\Controllers')->middleware('web')->group(function (){

    Route::get('payzone', 'PayzoneController@index');
    Route::post('cart', 'PayzoneController@cart')->name('payzone.cart');
    Route::get('payment', 'PayzoneController@payment')->name('payzone.action');

    Route::post('payment', 'PayzoneController@payment')->name('payzone.pay');

    Route::get('payment-process', 'ProcessController@index')->name('payzone.get');  // Not Required if no get request
    Route::post('payment-process', 'ProcessController@index')->name('payzone.process');
    Route::post('payment-success', 'ProcessController@result')->name('payzone.payment.success');
});
