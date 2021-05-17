<?php
class SnsExchangejpeasRequest
{
	private $apiParas = array();
	
	public function getApiMethodName(){
	  return "jingdong.jfdh.exchangejpeas";
	}
	
	public function getApiParas(){
		return json_encode($this->apiParas);
	}
	
	public function check(){
		
	}
	
	public function putOtherTextParam($key, $value){
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
                                                        		                                    	                   			private $clientId;
    	                        
	public function setClientId($clientId){
		$this->clientId = $clientId;
         $this->apiParas["clientId"] = $clientId;
	}

	public function getClientId(){
	  return $this->clientId;
	}

                        	                   			private $businessId;
    	                        
	public function setBusinessId($businessId){
		$this->businessId = $businessId;
         $this->apiParas["businessId"] = $businessId;
	}

	public function getBusinessId(){
	  return $this->businessId;
	}

                        	                        	                   			private $key;
    	                        
	public function setKey($key){
		$this->key = $key;
         $this->apiParas["key"] = $key;
	}

	public function getKey(){
	  return $this->key;
	}

                        	                   			private $signature;
    	                        
	public function setSignature($signature){
		$this->signature = $signature;
         $this->apiParas["signature"] = $signature;
	}

	public function getSignature(){
	  return $this->signature;
	}

                        	                   			private $integral;
    	                        
	public function setIntegral($integral){
		$this->integral = $integral;
         $this->apiParas["integral"] = $integral;
	}

	public function getIntegral(){
	  return $this->integral;
	}

                        	                   			private $jpeas;
    	                        
	public function setJpeas($jpeas){
		$this->jpeas = $jpeas;
         $this->apiParas["jpeas"] = $jpeas;
	}

	public function getJpeas(){
	  return $this->jpeas;
	}

                        	                   			private $remark;
    	                        
	public function setRemark($remark){
		$this->remark = $remark;
         $this->apiParas["remark"] = $remark;
	}
    
    #京豆=1  钢镚=4
    public function setOriginType($originType = 1) {
        $this->originType = $originType;
        $this->apiParas['originType'] = $originType;
    }
    
    public function getOriginType() {
        return $this->originType;
    }

	public function getRemark(){
	  return $this->remark;
	}

                        	                   			private $status;
    	                        
	public function setStatus($status){
		$this->status = $status;
         $this->apiParas["status"] = $status;
	}

	public function getStatus(){
	  return $this->status;
	}

                            }





        
 

