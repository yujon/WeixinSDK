<?php
namespace Controls;

class Account extends Common{
	
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
	
	//获取临时二维码
	function getTemQrCode($scene_id){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$access_token}";
		$arr = array("expire_seconds"=>604800,"action_name" =>"QR_SCENE","action_info"=>array("scene_id"=>$scene_id));
		$data = json_encode($arr);	
		$res = $this->http_curl($url,true,true,$data);
		if(isset($res->errcode)){
			print_r($res);
			return;
		}
		$ticket = $res->ticket;
		$url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket);
		$img = $this->http_curl($url,false);
        $filePath="public/qrcode/temporary/".md5($scene_id).".jpg";
        file_put_contents($filePath,$img);
        return $filePath;
	}
	
	//获取永久二维码
	function getForQrCode($scene_id){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$access_token}";
		$arr = array("action_name" =>"QR_LIMIT_SCENE","action_info"=>array("scene_id"=>$scene_id));
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		if(isset($res->errcode)){
			print_r($res);
			return;
		}
		$ticket = $res->ticket;
		$url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket);
		$img = $this->http_curl($url,false);
        $filePath="public/qrcode/forever/{$scene_id}.jpg";
        file_put_contents($filePath,$img);
        return $filePath;	
	}
	
	//长链接转成短链接
	function getShortUrl($longUrl){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/shorturl?access_token={$access_token}";
		$arr = array("action" =>"long2short","long_url"=>$longUrl);
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		if($res->errcode){
			print_r($res);
			return;
		}
		return $res->short_url;
	}
	
	
}