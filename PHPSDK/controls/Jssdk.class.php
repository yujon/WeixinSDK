<?php
namespace Controls;

class Jssdk extends Common{

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
	
	//获取jsapi_ticket
	function get_jsapi_ticket(){	   
	   $configs = \Classes\Configs::getInstance();
	   $arr = $configs->offsetGet("jsapi_ticket","runtime/datas");
	   if(!isset($arr['expiretime']) || !isset($arr['jsapi_ticket']) || (time()>$arr['expiretime'])){
	   	    $access_token = $this->getAccessToken();
		   	$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$access_token}&type=jsapi";
		   	$res = $this->http_curl($url,true);
		   	$arr['expiretime'] = time()+7200;
		   	$arr['jsapi_ticket'] = $res->ticket;
		   	$configs->offsetSet("jsapi_ticket", $arr,"runtime/datas");
	   }
	   $jsapi_ticket = $arr['jsapi_ticket'];
	   return $jsapi_ticket;
	}
	
	//获取随机字符串echostr
	function getNoncestr($length = 16){
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for($i=0;$i<$length;$i++){
			$str .= substr($chars,mt_rand(0,strlen($chars)-1),1);
		}
		return $str;
	}
	
	//获取签名
	function getSignature(){
		$timestamp = time();
		$nonceStr = $this->getNoncestr();
		$jsapi_ticket = $this->get_jsapi_ticket();
		$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		//按照key值字典序排序
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
		$signature = sha1($string);
		//封装数据
		$signPackage = array(
				"appId"     => $this->appid,
				"nonceStr"  => $nonceStr,
				"timestamp" => $timestamp,
				"url"       => $url,
				"signature" => $signature,
				"rawString" => $string
		);
		$json = json_encode($signPackage);	
		echo $json;
	}
	
	
}