<?php
class ComScriptCURL
{
	const CURL_TIMEOUT = 360000;
	/**
	 * download the url to a local file
	 *
	 * @param string $url       The url
	 * @param string $localFile The local file path
	 *
	 * @return string The local file path
	 */
	public static function downloadFile($url, $localFile, $timeout = null, $extraOpts = array())
	{
		$timeout = trim($timeout);
		$timeout = (!is_numeric($timeout) ? self::CURL_TIMEOUT : $timeout);
		$fp = fopen($localFile, 'w+');
		$options = array(
				CURLOPT_FILE    => $fp,
				CURLOPT_TIMEOUT => $timeout, // set this to 8 hours so we dont timeout on big files
				CURLOPT_URL     => $url
// 				,CURLOPT_PROXY   => 'proxy.bytecraft.internal:3128'
		);
		foreach($extraOpts as $key => $value)
			$options[$key] = $value;
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		curl_exec($ch);
		fclose($fp);
		curl_close($ch);
		return $localFile;
	}
	/**
	 * read from a url
	 *
	 * @param string  $url             The url
	 * @param int     $timeout         The timeout in seconds
	 * @param array   $data            The data we are POSTING
	 * @param string  $customerRequest The type of the post: DELETE or POST etc...
	 *
	 * @return mixed
	 */
	public static function readUrl($url, $timeout = null, array $data = array(), $customerRequest = '', $extraOpts = array())
	{
		$timeout = trim($timeout);
		$timeout = (!is_numeric($timeout) ? self::CURL_TIMEOUT : $timeout);
		$options = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => $timeout, // set this to 8 hours so we dont timeout on big files
				CURLOPT_URL     => $url
				,CURLOPT_SSL_VERIFYHOST     => 0 // ignore invalid ssl
				,CURLOPT_SSL_VERIFYPEER     => 0 // ignore invalid ssl
// 				,CURLOPT_PROXY   => 'proxy.bytecraft.internal:3128'
		);
		foreach($extraOpts as $key => $value)
			$options[$key] = $value;
		if(count($data) > 0)
		{
			if(trim($customerRequest) === '')
				$options[CURLOPT_POST] = true;
			else
				$options[CURLOPT_CUSTOMREQUEST] = $customerRequest;
			$options[CURLOPT_POSTFIELDS] = self::buildQuery($data);
			var_dump($options[CURLOPT_POSTFIELDS]);
		}
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$data =curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	public static function is404($url, $timeout = null, $extraOpts = array()) 
	{
		$timeout = trim($timeout);
		$timeout = (!is_numeric($timeout) ? self::CURL_TIMEOUT : $timeout);
		$options = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT        => $timeout, // set this to 8 hours so we dont timeout on big files
				CURLOPT_URL            => $url,
				CURLOPT_NOBODY         => true
// 				,CURLOPT_PROXY   => 'proxy.bytecraft.internal:3128'
		);
		foreach($extraOpts as $key => $value)
			$options[$key] = $value;
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$response =curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		/* If the document has loaded successfully without any redirection or error */
		if ($httpCode >= 200 && $httpCode < 300) {
			return false;
		} else {
			return true;
		}
	}
	public static function buildQuery($input, $numeric_prefix = '', $arg_separator = '&', $enc_type = 2, $keyvalue_separator = '=', $prefix = '') {
		if (is_array ( $input )) {
			$arr = array ();
			foreach ( $input as $key => $value ) {
				$name = $prefix;
				if (strlen ( $prefix )) {
					$name .= '[';
					if (! is_numeric ( $key )) {
						$name .= $key;
					}
					$name .= ']';
				} else {
					if (is_numeric ( $key )) {
						$name .= $numeric_prefix;
					}
					$name .= $key;
				}
				if ((is_array ( $value ) || is_object ( $value )) && count ( $value )) {
					$arr [] = self::buildQuery ( $value, $numeric_prefix, $arg_separator, $enc_type, $keyvalue_separator, $name );
				} else {
					if ($enc_type === 2) {
						$arr [] = rawurlencode ( $name ) . $keyvalue_separator . rawurlencode ( $value ?  : '' );
					} else {
						$arr [] = urlencode ( $name ) . $keyvalue_separator . urlencode ( $value ?  : '' );
					}
				}
			}
			return implode ( $arg_separator, $arr );
		} else {
			if ($enc_type === 2) {
				return rawurlencode ( $input );
			} else {
				return urlencode ( $input );
			}
		}
	}
}