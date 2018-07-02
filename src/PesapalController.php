<?php

namespace Patricmutwiri\Pesapal;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class PesapalController extends Controller
{
    public function paynow(Request $request){
    	//iframe
    	include_once('oauth.php');
    	$token = $params 	= NULL;
		$consumer_key 		= config('pesapal.consumer_key');
		$consumer_secret 	= config('pesapal.consumer_secret');
		$signature_method 	= config('pesapal.signature_method');
		$iframelink 		= config('pesapal.iframelink');
		$iframelivelink 	= config('pesapal.iframelivelink');
		$callback_url 		= config('pesapal.callback_url');

		$signature_method = new OAuthSignatureMethod_HMAC_SHA1();
		//get form details
		$amount 	= $request->papers;

		dd($amount);
		$amount 	= number_format($amount, 2);//format amount to 2 decimal places

		$desc 		= 'Newspapers';
		$type 		= 'MERCHANT';
		$reference 	= 'ORD'.uniqid(); //unique order id of the transaction, generated by merchant
		$first_name = Auth::user()->name;
		$last_name 	= $request->lastname;
		$email 			= Auth::user()->email;
		$phonenumber 	= '';//ONE of email or phonenumber is required
		$post_xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?><PesapalDirectOrderInfo xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" Amount=\"".$amount."\" Description=\"".$desc."\" Type=\"".$type."\" Reference=\"".$reference."\" FirstName=\"".$first_name."\" LastName=\"".$last_name."\" Email=\"".$email."\" PhoneNumber=\"".$phonenumber."\" xmlns=\"http://www.pesapal.com\" />";
		$post_xml = htmlentities($post_xml);
		$consumer = new OAuthConsumer($consumer_key, $consumer_secret);
		//post transaction to pesapal
		$iframe_src = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $iframelink, $params);
		$iframe_src->set_parameter("oauth_callback", $callback_url);
		$iframe_src->set_parameter("pesapal_request_data", $post_xml);
		$iframe_src->sign_request($signature_method, $consumer, $token);
		//display pesapal - iframe and pass iframe_src
		return view('pesapal::paynow', compact('iframe_src'));
    }

    public function status(Request $request){
    	$result = 'status view test';
		return view('pesapal::status', compact('result'));
    }
    
    public function transactions(Request $request){
    	$result = 'transactions view test';
		return view('pesapal::transactions', compact('result'));
    }

    public function pesapalcallback(Request $request){
    	// process callback
		dd($request);
    }

    public function ipn(Request $request){
    	// listen here
    	include_once('oauth.php');
		$consumer_key 		= config('pesapal.consumer_key');
		$consumer_secret 	= config('pesapal.consumer_secret');

		$statusrequestAPI = config('pesapal.statusrequestAPI');
		                   //https://www.pesapal.com/api/querypaymentstatus' when you are ready to go live!
		// Parameters sent to you by PesaPal IPN
		$pesapalNotification 	= 	$request->pesapal_notification_type;
		$pesapalTrackingId 		=	$request->pesapal_transaction_tracking_id;
		$pesapal_merchant_reference	=	$request->pesapal_merchant_reference;

		if($pesapalNotification=="CHANGE" && $pesapalTrackingId!='')
		{
		   $token = $params = NULL;
		   $consumer = new OAuthConsumer($consumer_key, $consumer_secret);
		   $signature_method = new OAuthSignatureMethod_HMAC_SHA1();

		   //get transaction status
		   $request_status = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $statusrequestAPI, $params);
		   $request_status->set_parameter("pesapal_merchant_reference", $pesapal_merchant_reference);
		   $request_status->set_parameter("pesapal_transaction_tracking_id",$pesapalTrackingId);
		   $request_status->sign_request($signature_method, $consumer, $token);

		   $ch = curl_init();
		   curl_setopt($ch, CURLOPT_URL, $request_status);
		   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		   curl_setopt($ch, CURLOPT_HEADER, 1);
		   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		   if(defined('CURL_PROXY_REQUIRED')) if (CURL_PROXY_REQUIRED == 'True')
		   {
		      $proxy_tunnel_flag = (defined('CURL_PROXY_TUNNEL_FLAG') && strtoupper(CURL_PROXY_TUNNEL_FLAG) == 'FALSE') ? false : true;
		      curl_setopt ($ch, CURLOPT_HTTPPROXYTUNNEL, $proxy_tunnel_flag);
		      curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
		      curl_setopt ($ch, CURLOPT_PROXY, CURL_PROXY_SERVER_DETAILS);
		   }

		   $response = curl_exec($ch);

		   $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		   $raw_header  = substr($response, 0, $header_size - 4);
		   $headerArray = explode("\r\n\r\n", $raw_header);
		   $header      = $headerArray[count($headerArray) - 1];

		   //transaction status
		   $elements = preg_split("/=/",substr($response, $header_size));
		   $status = $elements[1];

		   curl_close ($ch);
		   
		   //UPDATE YOUR DB TABLE WITH NEW STATUS FOR TRANSACTION WITH pesapal_transaction_tracking_id $pesapalTrackingId
		   $result = $resp.' | '.$status;
		   
		   if(DB_UPDATE_IS_SUCCESSFUL)
		   {
		      $resp="pesapal_notification_type=$pesapalNotification&pesapal_transaction_tracking_id=$pesapalTrackingId&pesapal_merchant_reference=$pesapal_merchant_reference";
		      ob_start();
		      echo $resp;
		      ob_flush();
		      exit;
		   }
		}

		return view('pesapal::ipn', compact('result'));
    }
}
