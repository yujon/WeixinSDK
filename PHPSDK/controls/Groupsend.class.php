<?php
namespace Controls;

class Groupsend extends Common{
	
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
	
	
	//群发图文消息
	/**
	 * @param boolean $preview
	 * @param string $msgtype
	 * @param array/string $content
	 * @param boolean $is_to_all
	 * @param boolean $groupid
	 * @param array/int $users
	 * @return string
	 */
	function sendToAll($preview=false,$msgtype,$content,$is_to_all=false,$groupid=true,$users){
		//$msgtype 1、mpnews图文，text文本，voice语音，image图片，wxcard卡卷，mpvideo视频
		$access_token = $this->getAccessToken();
		if($msgtype == "npnews"){
			//上传图片素材以及图文消息素材
			$imgUrl = "https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token={$access_token}";
			$articles = array();
			$newsUrl = "https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token={$access_token}";
			$i=0;
			foreach($content as $key=>$value){
				if($i<8){  //最多八条
					$i++;
					$filePath = $GLOBALS['root']."/public/{$value['img']['filename']}";
					if(file_exists($filePath)){
						$arr = array("media"=>"@{$filePath}","form-data" => $value['img']['imgInfo']);
						$data = json_encode($arr);
						$res = $this->http_curl($url,true,true,$data);
					}
					$articles['articles'][$key]['title'] = urlencode($value['article']['title']);    //标题
					$articles['articles'][$key]['thumb_media_id'] = $res->url;  //封面图片id
					$articles['articles'][$key]['author'] = urlencode($value['article']['author']);    //作者
					$articles['articles'][$key]['digest'] = urlencode($value['article']['digest']);    //摘要
					$articles['articles'][$key]['show_cover_pic'] = $value['article']['show_cover_pic'];  //是否显示封面图片
					$articles['articles'][$key]['content'] = urlencode($value['article']['content']);   //内容
					$articles['articles'][$key]['content_source_url'] = $value['article']['content_source_url'];   //阅读原文链接地址
				}
			}
			$data = urldecode(json_encode($articles));
			$res = $this->http_curl($url,true,true,$data);
			$media_id = $res->media_id;
			$arr = array(
					"filter"=>array("is_to_all"=>$is_to_all),
					"mpnews"=>array("media_id"=>$media_id),
					"msgtype"=>"mpnews"
			);
		}
		if($msgtype == "text"){
			$arr = array(
					"filter"=>array("is_to_all"=>$is_to_all),
					"text"=>array("content"=>$content),
					"msgtype"=>"text"
			);
		}
		if($msgtype == "voice" || $msgtype == "image" ||$msgtype == "mpvideo"){
			$material = new \Controls\Material();
			$fileInfo = array("filename"=>$content['filename'],"length"=>$content['length'],"content-type"=>$msgtype);
			if($msgtype == "mpvideo"){
				$media_id = $material->addForMaterial($num,$fileInfo,$content['description']);
				$videoUrl = "https://file.api.weixin.qq.com/cgi-bin/media/uploadvideo?access_token={$access_token}";
				$arr = array(
						"media_id"=>$media_id,
						"title"=>$content['title'],
						"description"=>$content['description']
				);
				$data = json_encode($arr);
				$res = $this->http_curl($url,true,true,$data);
				$media_id = $res->media_id;
			}else{
				$media_id = $material->addForMaterial($num,$fileInfo,$content['description']);
			}
			$arr = array(
					"filter"=>array("is_to_all"=>$is_to_all),
					$msgtype=>array("media_id"=>$media_id),
					"msgtype"=>$msgtype
			);
		}
	
		if($msgtype == "wxcard"){
			$arr = array(
					"filter"=>array("is_to_all"=>$is_to_all),
					"wxcard"=>array("card_id"=>$content),
					"msgtype"=>"wxcard"
			);
		}
	
		if($group_id){  //对群发对象进行处理
			$arr['filter']["group_id"] = $users;
		}else{
			foreach ($users as $key=>$val){
				$arr['filter']["touser"][$key]=$val;
			}
		}
		//根据分组进行群发
		if($preview){
			echo "sssss";exit;
			$url = "https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token={$access_token}";
		}else{
			$url = "https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token={$access_token}";
		}
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		return $res->msg_id;
	}
	
	//删除群发消息
	/**
	 *
	 * @param int $msg_id
	 * @return boolean
	 */
	function deleteSend($msg_id){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/message/mass/delete?access_token={$access_token}";
		$arr = array("msg_id"=>$msg_id);
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		return $res;
	}
	
	//查询群发消息发送状态
	function getSendStatus($msg_id){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/message/mass/get?access_token={$access_token}";
		$arr = array("msg_id"=>$msg_id);
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		return $res;
	}
	
}