<?php
namespace Controls;

class Response extends Common{
	
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
	
	//1、回复纯文本消息
	function fetchText($toUser,$fromUser,$content){
		$template = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					</xml>";
		echo sprintf($template, $toUser, $fromUser, time(),$content);
	}
	
	
	//2、回复图片消息
	function fetchImage($toUser,$fromUser,$media_id){
		$template = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[image]]></MsgType>
				<Image>
				<MediaId><![CDATA[%s]]></MediaId>
				</Image>
				</xml>";
		echo sprintf($template, $toUser, $fromUser, time(),$media_id);
	}
	
	//3、回复语音消息
	function fetchVoice($toUser,$fromUser,$media_id){
		$template = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[voice]]></MsgType>
					<Voice>
					<MediaId><![CDATA[%s]]></MediaId>
					</Voice>
					</xml>";
		echo sprintf($template, $toUser, $fromUser, time(),$media_id);
	}
	
	//4、回复视频消息
	function fetchVideo($toUser,$fromUser,$media_id,$title=null,$description=null){
		$template = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[video]]></MsgType>
					<Video>
					<MediaId><![CDATA[%s]]></MediaId>
					<Title><![CDATA[%s]]></Title>
					<Description><![CDATA[%s]]></Description>
					</Video>
					</xml>";
		echo sprintf($template, $toUser, $fromUser, time(),$media_id,$title,$description);
	}
	
	//5、回复音乐消息
	function fetchMusic($toUser,$fromUser,$title=null,$descrition=null,$musicUrl,$HQMusicUrl,$ThumbMediaId){
		$template = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[music]]></MsgType>
					<Music>
					<Title><![CDATA[%s]]></Title>
					<Description><![CDATA[%s]]></Description>
					<MusicUrl><![CDATA[%s]]></MusicUrl>
					<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
					<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
					</Music>
					</xml>";
		echo sprintf($template, $toUser, $fromUser, time(),$title,$descrition,$musicUrl,$HQMusicUrl,$ThumbMediaId);
	}
	
	//6、回复单图文或者多图文
	function fetchNews($toUser,$fromUser,$content){
		$template = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[news]]></MsgType>
						<ArticleCount>".count($ontent)."</ArticleCount>
						<Articles>";
		foreach($content as $k=>$v){
			$template .="<item>
						<Title><![CDATA[".$v['title']."]]></Title>
						<Description><![CDATA[".$v['description']."]]></Description>
						<PicUrl><![CDATA[".$v['picUrl']."]]></PicUrl>
						<Url><![CDATA[".$v['url']."]]></Url>
						</item>";
		}
		$template .="</Articles>
				    </xml> ";
		echo sprintf($template, $toUser, $fromUser, time());
	}

	
	
}