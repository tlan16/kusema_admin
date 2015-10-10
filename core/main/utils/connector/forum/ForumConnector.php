<?php
abstract class ForumConnector
{
	const CONNECTOR_TYPE_QUESTION = 'Question';
	const CONNECTOR_TYPE_ANSWER = 'Answer';
	const CONNECTOR_TYPE_COMMENTS = 'Comments';
	const CONNECTOR_TYPE_PERSON = 'Person';
	const CONNECTOR_TYPE_TOPIC = 'Topic';
	const CONNECTOR_TYPE_UNIT = 'Unit';
	/**
	 * debug mode switch
	 * 
	 * @var bool
	 */
	protected $debug = false;
	/**
	 * The session string
	 *
	 * @var string
	 */
	protected $_session;
	/**
	 * The cache for all the connector objects
	 *
	 * @var array
	 */
	public static $_cache;
	/**
	 * The REST host
	 *
	 * @var string
	 */
	protected $_rest;
	/**
	 * The user for the REST
	 *
	 * @var string
	 */
	protected $_apiUser;
	/**
	 * The key for the REST
	 *
	 * @var string
	 */
	protected $_apiKey;
	/**
	 * get the connector for external system
	 * 
	 * @param string $type
	 * @param string $rest
	 * @param string $apiUser
	 * @param string $apiKey
	 * @param bool	 $debug
	 * 
	 * @return ForumConnector
	 */
	public static function getConnector($type, $rest, $apiUser, $apiKey, $debug = false)
	{
		$key = md5(($type=trim($type)) . ($rest=trim($rest)) . ($apiUser=trim($apiUser)) . ($apiKey=trim($apiKey)));
		$className = $type . 'Connector';
		$class= new $className($rest, $apiUser, $apiKey);
		$class->debug = (intval($debug) === 1);
		return $class;
	}
	/**
	 * get the data from external system by url (REST)
	 * 
	 * @param string $url
	 * @param array	 $attributes
	 * @param bool	 $isJson
	 * @param bool	 $autoPopulate
	 */
	protected function getData($url, $attributes = array(), $isJson = true, $autoPopulate = false)
	{
		$url = trim($url);
		if($autoPopulate !== true)
		{
			$url = ($url . (strpos($url, '?') === false ? '?' : '&') . "populate=" . json_encode($attributes));
			if($this->debug === true)
				echo __FUNCTION__ . ': url => "' . $url . '"' . PHP_EOL;
			$data = ComScriptCURL::readUrl($url);
			if($isJson === true)
				$data = json_decode($data, true);
			return $data;
		}
		if($this->debug === true)
			echo __FUNCTION__ . ': url => "' . $url . '"' . PHP_EOL;
		$data = ComScriptCURL::readUrl($url);
		if($isJson === true)
			$data = json_decode($data, true);
		foreach ($data as $row)
		{
			$keys = array_keys($row);
			foreach ($keys as $key)
				self::addExtraAttribute($attributes, 'path', $key);
			break;
		}
		if($this->debug === true)
			echo __CLASS__ . ': url => "' . $url . '"' . PHP_EOL;
		$data = ComScriptCURL::readUrl($url . "?populate=" . json_encode($attributes));
		if($isJson === true)
			$data = json_decode($data, true);
		return $data;
	}
	/**
	 * add extra unique attribute to existing attributes
	 * 
	 * @param array		$attributes
	 * @param string 	$name
	 * @param string 	$value
	 * @param bool		$ignoreHidden
	 * 
	 * @throws Exception
	 */
	public static function addExtraAttribute(&$attributes, $name, $value, $ignoreHidden = true)
	{
		if(trim('name') === '')
			throw new Exception('Invalid name passed in');
		if($ignoreHidden === true && substr($value, 0, 1) !== '_')
		{
			$extra = array(
					array($name => $value)
			);
			$extra = array_merge($attributes, $extra);
			$extra = array_unique($extra, SORT_REGULAR);
			$attributes = $extra;
		}
	}
	/**
	 * constructor
	 *
	 * @param string $rest
	 * @param string $apiUser
	 * @param string $apiKey
	 */
	public function __construct($rest, $apiUser, $apiKey)
	{
		$this->_rest = trim($rest);
		$this->_apiUser = trim($apiUser);
		$this->_apiKey = trim($apiKey);
	}
	/**
	 * Getting the REST
	 *
	 * @return string
	 */
	protected function _getREST()
	{
		return trim($this->_rest);
	}
	/**
	 * Getting the api user
	 *
	 * @return string
	 */
	protected function _getApiUser()
	{
		return trim($this->_apiUser);
	}
	/**
	 * Getting the api key
	 *
	 * @return string
	 */
	protected function _getApiKey()
	{
		return trim($this->_apiKey);
	}
	/**
	 * process result field from external system
	 * 
	 * @param array		$obj
	 * @param string	$key
	 * @param unknown	$default
	 */
	public static function processField($obj, $key, $default = '')
	{
		return (isset($obj[$key]) ? $obj[$key] : $default);
	}
}