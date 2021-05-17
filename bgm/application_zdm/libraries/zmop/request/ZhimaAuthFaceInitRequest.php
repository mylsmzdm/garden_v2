<?php
/**
 * ZHIMA API: zhima.auth.face.init request
 *
 * @author auto create
 * @since 1.0, 2016-01-20 11:57:19
 */
class ZhimaAuthFaceInitRequest
{
	/** 
	 * 应用的标识
	 **/
	private $apiKey;
	
	/** 
	 * 参数的加密串
	 **/
	private $authMsg;
	
	/** 
	 * 不同商户的bundle_id,用来做缓存
	 **/
	private $bundleId;
	
	/** 
	 * 请求的sessionId
	 **/
	private $token;

	private $apiParas = array();
	private $fileParas = array();
	private $apiVersion="1.0";
	private $scene;
	private $channel;
	private $platform;
	private $extParams;

	
	public function setApiKey($apiKey)
	{
		$this->apiKey = $apiKey;
		$this->apiParas["api_key"] = $apiKey;
	}

	public function getApiKey()
	{
		return $this->apiKey;
	}

	public function setAuthMsg($authMsg)
	{
		$this->authMsg = $authMsg;
		$this->apiParas["auth_msg"] = $authMsg;
	}

	public function getAuthMsg()
	{
		return $this->authMsg;
	}

	public function setBundleId($bundleId)
	{
		$this->bundleId = $bundleId;
		$this->apiParas["bundle_id"] = $bundleId;
	}

	public function getBundleId()
	{
		return $this->bundleId;
	}

	public function setToken($token)
	{
		$this->token = $token;
		$this->apiParas["token"] = $token;
	}

	public function getToken()
	{
		return $this->token;
	}

	public function getApiMethodName()
	{
		return "zhima.auth.face.init";
	}

	public function setScene($scene)
	{
		$this->scene=$scene;
	}

	public function getScene()
	{
		return $this->scene;
	}
	
	public function setChannel($channel)
	{
		$this->channel=$channel;
	}

	public function getChannel()
	{
		return $this->channel;
	}
	
	public function setPlatform($platform)
	{
		$this->platform=$platform;
	}

	public function getPlatform()
	{
		return $this->platform;
	}

	public function setExtParams($extParams)
	{
		$this->extParams=$extParams;
	}

	public function getExtParams()
	{
		return $this->extParams;
	}	

	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function getFileParas()
	{
		return $this->fileParas;
	}

	public function setApiVersion($apiVersion)
	{
		$this->apiVersion=$apiVersion;
	}

	public function getApiVersion()
	{
		return $this->apiVersion;
	}

}
