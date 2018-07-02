<?php

	return [
		'consumer_key' 		=> env('consumer_key', 'gC4ujTLUuoHJvDCAEI+jRTsnFhrAPLcG'),
		'consumer_secret' 	=> env('consumer_secret', 'PJEOnS42ev5f2He5ybrqvMusl78='),
		'signature_method' 	=> env('signature_method', 'new OAuthSignatureMethod_HMAC_SHA1()'), // you can leave as default
		'iframelink' 		=> env('iframelink', 'https://demo.pesapal.com/api/PostPesapalDirectOrderV4'),
		'iframelivelink' 	=> env('iframelivelink', 'https://www.pesapal.com/API/PostPesapalDirectOrderV4'),
		'callback_url' 		=> env('callback_url', 'http://localhost/epaper/public/pesapalcallback'),
		'statusrequestAPI' 	=> env('statusrequestAPI', 'http://demo.pesapal.com/api/querypaymentstatus'),//change to      
		//https://www.pesapal.com/api/querypaymentstatus' when you are ready to go live!
	];