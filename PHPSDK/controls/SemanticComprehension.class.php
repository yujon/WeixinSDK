<?php
namespace Controls;

class SemanticComprehension extends Common{

	
	private static $instance;
	
	//单例模式
	public static function getInstance(){
		if(!(self::$instance instanceof self)){
			self::$instance = new self();
		}else{
			self::$instance->resetInfo();
		}
		return self::$instance;
	}
 
	function query($query,$city,$category,$uid){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/semantic/semproxy/search?access_token={$access_token}";
		$arr = array(
				"query"=>urlencode($query),
				"city"=>urlencode($city),
				"category"=>$category,
				"appid"=>$this->appid,
				"uid"=>$uid
		);
		$data = urldecode(json_encode($arr));
		$res = $this->http_curl($url,true,true,$data);
		return $res;		
	}
	
}