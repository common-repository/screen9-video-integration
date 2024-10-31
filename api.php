<?php
/** Lets load the WordPress Environment and the xml-rpc lib */

require_once (ABSPATH . '/wp-includes/class-IXR.php');

/**
 * Make an API call to Screen9
 *
 * @param string $function
 *   The operation to we want the API to perform.
 *
 * @param array $arguments
 *   (optional) Array of function specific arguments.
 *   Defaults to an empty array.
 *   
 * @param key $string
 *   (optional) If set cache the request using the supplied key
 *
 * @return array
 *   The requested data from the API.
 */
function screen9_call($function, $arguments, $key = null) {

	$response = wp_cache_get( $key, SCREEN9_OBJ_CACHE_GRP );

	if ( false === $response ) {
		$common = array(
	    'version' => '2.0',
	    'custid'  => get_option('screen9_customer_id'),
	    'refer'   => "unknown",
	    'userip'  => "0.0.0.0",
	    'browser' => 'wordpress',
		);

		$client = new IXR_Client(get_option('screen9_api_url'));

		$merged_arguments = array_merge(array($function, $common), $arguments);

		call_user_func_array(array($client,'query'), $merged_arguments);

		if($client->isError()){
			$response=('Felkod: '.$client->getErrorCode().'<br/>Felmeddelande: '.$client->getErrorMessage());
		}else{
			$response=($client->getResponse());
		}

		if ( !empty( $response ) && $key ){
			wp_cache_set( $key, $response, SCREEN9_OBJ_CACHE_GRP);
		}
	}
	return $response;
}

?>