<?php
namespace Controls;

class Material extends Common{
	
	public $types = array(
		"image","voice","video","thumb"
	);
	
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
	
    //上传临时多媒体素材	
	function addTemMaterial($num,$fileInfo){
		$type = $this->types[$num];
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token={$access_token}&type={$type}";
		$filePath = $GLOBALS['root']."/public/{$fileInfo['filename']}";
		$arr = array("media"=>"@{$filePath}","form-data" => $fileInfo);
		$data=json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);		   
		if(!isset($res->errcode)){
			return $res->media_id;
		}
	}
	
	//获取临时多媒体素材
	function getTemMaterial($num,$media_id){
	   $access_token = $this->getAccessToken();
	   if($num == 2){
	   	    $url = "http://api.weixin.qq.com/cgi-bin/media/get?access_token={$access_token}&media_id={$media_id}";
	   }else{
	   	    $url = "https://api.weixin.qq.com/cgi-bin/media/get?access_token={$access_token}&media_id={$media_id}";
	   }	  
	   $res = $this->http_curl($url,false);
	   if(!isset($res->errcode)){
	   	  file_put_contents("public/upload/{$media_id}.jpg",$res);
	   }
	}
	
	//上传永久多媒体素材
	function addForMaterial($num,$fileInfo,$description=null){ //0image、1voice、2video、3thumb
		$type = $this->types[$num];
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token={$access_token}&type={$type}";
		$filePath = $GLOBALS['root']."/public/{$fileInfo['filename']}";
		$arr = array("media"=>"@{$filePath}","form-data" => $fileInfo);
		if($num == 2){
			$arr['description']['title'] = $fileInfo['filename']."_video";
			$arr['description']["description"] = $description;
		}
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$$data);
		if(!isset($res->errcode)){
			return $res->media_id;
		}else{
			print_r($res);
		}
	}
	
    //上传永久图文素材
	function addForNews($fileInfo,$arr){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/material/add_news?access_token={$access_token}";
		$articles = array();
		foreach($arr as $key=>$article){
			$articles['articles'][$key]['title'] = urlencode($article[0]);    //标题
			$articles['articles'][$key]['thumb_media_id'] = $article[1];  //封面图片id
			$articles['articles'][$key]['author'] = urlencode($article[2]);    //作者
			$articles['articles'][$key]['digest'] = urlencode($article[3]);    //摘要
			$articles['articles'][$key]['show_cover_pic'] = $article[4];  //是否显示封面图片
			$articles['articles'][$key]['content'] = urlencode($article[5]);   //内容
			$articles['articles'][$key]['content_source_url'] = $article[6];   //阅读原文链接地址
		}		
		$data = urldecode(json_encode($articles));
		$res = $this->http_curl($url,true,true,$data);
		if(!isset($res->errcode)){
			print_r($res);
			return $res->media_id;
		}
	}
	
	//修改永久图文消息
	function editForNews($fileInfo,$media_id,$index,$arr){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/material/update_news?access_token={$access_token}";
		$articles = array("media_id"=>$media_id,"index"=>$index);		
		foreach($arr as $key=>$article){
			$articles['articles'][$key]['title'] = urlencode($article[0]);    //标题
			$articles['articles'][$key]['thumb_media_id'] = $article[1];  //封面图片id
			$articles['articles'][$key]['author'] = urlencode($article[2]);    //作者
			$articles['articles'][$key]['digest'] = urlencode($article[3]);    //摘要
			$articles['articles'][$key]['show_cover_pic'] = $article[4];  //是否显示封面图片
			$articles['articles'][$key]['content'] = urlencode($article[5]);   //内容
			$articles['articles'][$key]['content_source_url'] = $article[6];   //阅读原文链接地址
		}
		$data = urldecode(json_encode($articles));
		$res = $this->http_curl($url,true,true,$data);
		if(isset($res->errcode)){ // 成功时$res->errcode为0
			print_r($res);
		}
	}
	
	//获取永久多媒体素材
	function getForMaterial($num,$media_id){
		$access_token = $this->getAccessToken();	
		$url = "https://api.weixin.qq.com/cgi-bin/material/get_material?access_token={$access_token}";
		$arr = array("media_id"=>$media_id);
		$data = json_encode($arr);
		$res = $this->http_curl($url,false,true,$data);
		if($res){
			if($num != 4){  //如果不是图文
				file_put_contents("public/upload/{$media_id}.jpg",$res);
			}else{
				file_put_contents("public/articles/{$media_id}.txt",$res);
			}		
		}
	}
	
	//删除永久多媒体素材
	function delForMaterial($media_id){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/material/del_material?access_token={$access_token}";
		$arr = array("media_id"=>$media_id);
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		if(isset($res->errcode)){
			print_r($res);
		}
		return true;
	}
	
	//获取各种永久多媒体素材数量
	function getMaterialNum(){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token={$access_token}";
		$res = $this->http_curl($url);
		if($res){
			print_r($res);
			return ;
		}
		return $res;
	}
	
	//获取永久多媒体素材列表
	function getMaterialList($num,$offset,$count){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token={$access_token}";
		$arr = array( 
				"type"=>$this->types[$num],
		        "offset"=>$offset,
		        "count"=>$count
		);
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		if(isset($res->errcode)){
			print_r($res);
			return ;
		}
		return $res;
	}
	

}