<?php
/**
 * ZHIMA API: zhima.credit.xueji.verify request
 *
 * @author auto create
 * @since 1.0, 2016-01-20 12:00:03
 */
class ZhimaCreditXuejiVerifyRequest
{
	/** 
	 * 院校名称
	 **/
	private $collegeName;
	
	/** 
	 * 学历层次：博士、硕士、本科、专科、不详。
	 **/
	private $educationDegree;
	
	/** 
	 * 入学年份，格式为四位数的年份，如2013
	 **/
	private $enrollmentYear;
	
	/** 
	 * 毕业年份，格式为四位数的年份，如2018
	 **/
	private $graduateYear;
	
	/** 
	 * 芝麻会员在商户端的身份标识
	 **/
	private $openId;
	
	/** 
	 * 产品码
	 **/
	private $productCode;
	
	/** 
	 * 商户传入的业务流水号。此字段由商户生成，需确保唯一性，用于定位每一次请求，后续按此流水进行对帐。生成规则: 固定30位数字串，前17位为精确到毫秒的时间yyyyMMddHHmmssSSS，后13位为自增数字。
	 **/
	private $transactionId;

	private $apiParas = array();
	private $fileParas = array();
	private $apiVersion="1.0";
	private $scene;
	private $channel;
	private $platform;
	private $extParams;

	
	public function setCollegeName($collegeName)
	{
		$this->collegeName = $collegeName;
		$this->apiParas["college_name"] = $collegeName;
	}

	public function getCollegeName()
	{
		return $this->collegeName;
	}

	public function setEducationDegree($educationDegree)
	{
		$this->educationDegree = $educationDegree;
		$this->apiParas["education_degree"] = $educationDegree;
	}

	public function getEducationDegree()
	{
		return $this->educationDegree;
	}

	public function setEnrollmentYear($enrollmentYear)
	{
		$this->enrollmentYear = $enrollmentYear;
		$this->apiParas["enrollment_year"] = $enrollmentYear;
	}

	public function getEnrollmentYear()
	{
		return $this->enrollmentYear;
	}

	public function setGraduateYear($graduateYear)
	{
		$this->graduateYear = $graduateYear;
		$this->apiParas["graduate_year"] = $graduateYear;
	}

	public function getGraduateYear()
	{
		return $this->graduateYear;
	}

	public function setOpenId($openId)
	{
		$this->openId = $openId;
		$this->apiParas["open_id"] = $openId;
	}

	public function getOpenId()
	{
		return $this->openId;
	}

	public function setProductCode($productCode)
	{
		$this->productCode = $productCode;
		$this->apiParas["product_code"] = $productCode;
	}

	public function getProductCode()
	{
		return $this->productCode;
	}

	public function setTransactionId($transactionId)
	{
		$this->transactionId = $transactionId;
		$this->apiParas["transaction_id"] = $transactionId;
	}

	public function getTransactionId()
	{
		return $this->transactionId;
	}

	public function getApiMethodName()
	{
		return "zhima.credit.xueji.verify";
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
