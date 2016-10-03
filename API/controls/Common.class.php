<?php
namespace Controls;

class Common extends \Classes\Action{

	protected $token;
	protected $appid;
	protected $appsecret;
	protected $starttime;
	protected $access_token;	
	
	protected function __construct(){
		$configs  = \Classes\Configs::getInstance();
		$temp = $configs->offsetGet("temp");
		$this->token = $temp['token'];
		$this->appid = $temp['appid'];
		$this->appsecret = $temp['appsecret'];
		$this->refreshAccessToken();
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

		if( curl_errno($ch) ){
			var_dump(curl_error($ch));
			return false;
		}
		//4.关闭curl
		curl_close( $ch );
		if($json){
			$res = json_decode($res);
		}   
		return $res;
	}
	
	//刷新access_token;
	function refreshAccessToken(){
		//1.请求url地址
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;		
		$arr = $this->http_curl($url);
		$this->access_token = $arr->access_token;
		$this->starttime = time();
	}
	
	//获取Access_token值
	function getAccessToken(){
		$now = time();
        if(isset($this->access_token)&& ($now - $this->starttime < 7200)){
        	return $this->access_token;
        }
        $this->refreshAccessToken();
		return $this->access_token;
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