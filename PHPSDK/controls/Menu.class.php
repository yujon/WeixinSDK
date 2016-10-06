<?php
namespace Controls;

class Menu extends Common{
	
	public $type = array(
			"click", "view","scancode_push","scancode_waitmsg","pic_sysphoto",
			"pic_photo_or_album","pic_weixin","location_select","media_id","view_limited"
	);
		// 	0、click：点击推事件 
		// 	1、view：跳转URL
		// 	2、scancode_push：扫码推事件
		// 	3、scancode_waitmsg：扫码推事件且弹出“消息接收中”提示框
		// 	4、pic_sysphoto：弹出系统拍照发图
		// 	5、pic_photo_or_album：弹出拍照或者相册发图
		// 	6、pic_weixin：弹出微信相册发图器
		// 	7、location_select：弹出地理位置选择器
		// 	8、media_id：下发消息（除文本消息）
		// 	9、view_limited：跳转图文消息URL	
	
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
	
	function create(){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}";
		$configs= \Classes\Configs::getInstance();
		
		$menu = $configs->offsetGet("menu");
		foreach ($menu['button'] as $key=>$button){
			$menu['button'][$key]['name'] = urlencode($button['name']);
			foreach ($button['sub_button'] as $sub_key=>$sub_button){
				$menu['button'][$key]['sub_button'][$sub_key]['name'] = urlencode($sub_button['name']);
			}
		}
		$data = urldecode(json_encode($menu));
		$res = $this->http_curl($url,true,true,$data);
		return true;
	}
	
	function get(){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token={$access_token}";		
		$res = $this->http_curl($url);
		return true;
	}
	
	function delete(){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token={$access_token}";
		$res = $this->http_curl($url);
		return true;
	}
	
	
	
}