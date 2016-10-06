<?php
namespace Controls;

class Template extends Common{
	
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
	
	//设置所属行业
	function setIndustry(){
		$access_token = $this->getAccessToken($industry_1,$industry_2);
		$url = "https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token={$access_token}";
		$arr = array("industry_id1"=>$industry_1,"industry_id2"=>$industry_id2);
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		return true;
	}
	
	//获取所属行业信息
	function getIndustry(){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/template/get_industry?access_token={$access_token}";
		$res = $this->http_curl($url,true);
		return $res;
	}
	
	//获取模板ID
	function getTplID($tpl_id){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token={$access_token}";
		$arr = array("template_id_short"=>$tpl_id);
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		return $res->template_id;
	}
	
	//获取模板列表
	function getTplID(){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token={$access_token}";
		$res = $this->http_curl($url,true);
		return $res->template_list;
	}
	
	//删除模板
	function getTplID($tpl_id){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token={$access_token}";
		$arr = array("template_id"=>$tpl_id);
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		return true;
	}
	
	//发送模板消息
	function sendTplMsg($openid,$tpl_id,$arr){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}";
		$arr = array(
				"touser"=>$openid,
				"template_id"=>$tpl_id,
				"url"=>$arr['url'],
				"data"=>array(
						"name"=>array(
								"value"=>$openid,
								"color"=>"#173177"
						),
						"date"=>array(
								"value"=>date("Y-m-d"),
								"color"=>"#173177"
						)
				)
		);
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		return $res;
	}
	
	
}