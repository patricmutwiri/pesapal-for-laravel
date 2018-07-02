<?php
	Route::get('pesapal', 'PesapalController@pesapal')->name('pesapal');
	Route::get('status', 'PesapalController@status')->name('status');
	Route::get('paynow', 'PesapalController@paynow')->name('paynow');
	Route::post('paynow', 'PesapalController@paynow')->name('paynow');
	Route::get('transactions', 'PesapalController@transactions')->name('transactions');
	Route::get('pesapalcallback', 'PesapalController@pesapalcallback')->name('pesapalcallback');
	Route::get('ipn', 'PesapalController@ipn')->name('ipn');
	Route::post('ipn', 'PesapalController@ipn')->name('ipn');
