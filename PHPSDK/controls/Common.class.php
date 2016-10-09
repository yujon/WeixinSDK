<?php
namespace Controls;

class Common extends \Classes\Action{

	protected $token;
	protected $appid;
	protected $appsecret;
	
	protected function __construct(){
		$configs  = \Classes\Configs::getInstance();
		$temp = $configs->offsetGet("temp");
		$this->token = $temp['token'];
		$this->appid = $temp['appid'];
		$this->appsecret = $temp['appsecret'];
	}
	
	//初始化
	function init(){
		
	}
	

	//采集信息
	function http_curl($url,$json=true,$post=false,$data=null){
		//1.初始化curl
		$ch = curl_init();
		//2.设置curl的参数
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_NOBODY, 0);  //只取body
		if($post){ //如果是post方式传输
			curl_setopt($ch, CURLOPT_POST, 1);
			@curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		//调用接口
		$res = curl_exec($ch);
// 		$httpHeader  = curl_getinfo($ch);

		if( curl_errno($ch)){
			$logstr = "[".curl_errno($ch)."]:".curl_error($ch);
			trigger_error($logstr);  //写错误日志
			return false;
		}
		
		//4.关闭curl
		curl_close( $ch );
		if($json){
			$res = json_decode($res);
		}  

		
		return $res;
	}
	
	//获取Access_token值
	function getAccessToken(){
		$configs = \Classes\Configs::getInstance();
		$arr = $configs->offsetGet("access_token","runtime/datas");
		if(!isset($arr['expiretime']) || !isset($arr['access_token']) || (time()>$arr['expiretime'])){
			//1.请求url地址
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
			$res = $this->http_curl($url,true);
			$arr['expiretime'] = time()+7200;
			$arr['access_token'] = $res->access_token;
			$configs->offsetSet("access_token", $arr,"runtime/datas");
		}
		$access_token = $arr['access_token'];
		return $access_token;
	}
	
	//获取微信服务器IP列表
	function getWxServerIp(){
		$accessToken = $this->getWxAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=".$accessToken;
		$res = $this->http_curl($url);
		echo "<pre>";
		var_dump( $res );
		echo "</pre>";
	}
}