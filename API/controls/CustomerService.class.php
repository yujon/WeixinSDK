<?php
namespace Controls;

class CustomerService extends Common{

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
	
	//添加客服账号
	function  addServiceAccount($kf_account,$nickname,$password){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/customservice/kfaccount/add?access_token={$access_token}";
		$arr = array(
				"kf_account"=>$kf_account,
				"nickname"=>$nickname,
				"password"=>$password
		);
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		if($res->errcode){
			print_r($res);
			return false;
		}
		return true;
	}
	
    //删除客服账号
    function  updateServiceAccount($kf_account,$nickname,$password){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/customservice/kfaccount/del?access_token={$access_token}";
		$arr = array(
				"kf_account"=>$kf_account,
				"nickname"=>$nickname,
				"password"=>$password
		);
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		if($res->errcode){
			print_r($res);
			return false;
		}
		return true;
	}
	
    //设置客服头像
	function  updateServiceAccount($kf_account,$fileInfo){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/customservice/kfaccount/uploadheadimg?access_token={$access_token}";
		$filePath = $GLOBALS['root']."/public/{$fileInfo['filename']}";
		$arr = array("media"=>"@{$filePath}","form-data" => $fileInfo);
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);		   
		if($res->errcode){
			print_r($res);
			return false;
		}
		return true;
	}
	
	//获取所有客服账号
	function getAccountList(){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/customservice/kfaccount/getkflist?access_token={$access_token}";
		$res = $this->http_curl($url,true);
		if(isset($res->errcode)){
			print_r($res);
			return false;
		}
		return $res->kf_list;
	}
	
	//发送客服消息
	function sendTextMsg($openid,$content){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
		$arr = array(
				"touser"=>$openid,
				"msgtype"=>"text",
				"text"=>array("content"=>$content)
		);
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		if(isset($res->errcode)){
			print_r($res);
			return false;
		}
		return true;
	}
	
	//发送多媒体消息,image/voice/video
	function sendMediaMsg($openid,$msgtype,$media_id,$title=null,$description=null){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
		$arr = array(
				"touser"=>$openid,
				"msgtype"=>$msgtype,
				$msgtype=>array("media_id"=>$media_id)
		);
		if($msgtype == "video"){
			$arr[$msgtype]['thumb_media_id'] = $media_id;
			$arr[$msgtype]['title'] = $title;
			$arr[$msgtype]['description'] = $description;
			
		}
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		if(isset($res->errcode)){
			print_r($res);
			return false;
		}
		return true;
	}
	
	//发送音乐消息
	function sendMusicMsg($openid,$title,$description,$musicurl,$hqmusicurl,$thumb_media_id){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
		$arr = array(
				"touser"=>$openid,
				"msgtype"=>"music",				
				"music"=>array(
						"title"=>$title,
						"description"=>$description,
						"musicurl"=>$musicurl,
						"hqmusicurl"=>$hqmusicurl,
						"thumb_media_id"=>$thumb_media_id						
				)
		);
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		if(isset($res->errcode)){
			print_r($res);
			return false;
		}
		return true;
	}
	
	//发送图文消息
	function sendNewsMsg($openid,$articles,$toOut=true){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
		if($toOut){
			$arr = array(
					"touser"=>$openid,
					"msgtype"=>"news",
					"news"=>array(
							"articles"=>array()
					)
			);
			foreach ($articles as $key=>$val){
				$arr['news']['articles'][$key]= $val;
			}
		}else{
			$arr = array(
					"touser"=>$openid,
					"msgtype"=>"mpnews",
					"npnews"=>array(
							"articles"=>array()
					)
			);
			foreach ($articles as $key=>$val){
				$arr['npnews']['articles'][$key]= $val;
			}
		}
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		if(isset($res->errcode)){
			print_r($res);
			return false;
		}
		return true;
	}
	
	//发送卡券消息
	function sendWxcard($openid,$card_id,$card_ext){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
		$arr = array(
				"touser"=>$openid,
				"msgtype"=>"wxcard",
				"wxcard"=>array(
						"card_id"=>$card_id,
						"card_ext"=>$card_ext
						)
		);
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		if(isset($res->errcode)){
			print_r($res);
			return false;
		}
		return true;
	}
}