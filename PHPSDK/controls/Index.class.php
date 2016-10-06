<?php
namespace Controls;

class Index extends Common{	
	
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
	
	/*
	 *1 将timestamp，nonce，token按字典树排序
	*2 将三个参数进行shal加密
	*3 将加密后的字符串与signature进行对比判断是否是微信传过来的
	*/
	public function index(){
		//获得参数 signature nonce token timestamp echostr
		$nonce     = $_GET['nonce'];
		$token     = $this->token;
		$timestamp = $_GET['timestamp'];
		$echostr   = $_GET['echostr'];
		$signature = $_GET['signature'];
		//形成数组，然后按字典序排序
		$array = array();
		$array = array($nonce, $timestamp, $token);
		sort($array);
		//拼接成字符串,sha1加密 ，然后与signature进行校验
		$str = sha1( implode( $array ) );
		if( $str  == $signature && $echostr ){
			//第一次接入weixin api接口的时候
			echo  $echostr;
			exit;
		}else{
			$this->responseMsg();
		}
	}
	
	
	// 接收数据包并判断类型回复相应消息
	public function responseMsg(){
			/*<xml>
			 <ToUserName><![CDATA[toUser]]></ToUserName>
			<FromUserName><![CDATA[FromUser]]></FromUserName>
			<CreateTime>123456789</CreateTime>
			<MsgType><![CDATA[event]]></MsgType>
			<Event><![CDATA[subscribe]]></Event>
			</xml>*/
			//1.获取到微信推送过来post数据（xml格式）
			$postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
			//2.处理消息类型，并设置回复类型和内容
			$postObj = simplexml_load_string( $postArr );
			//判断该数据包是否是什么类型
			
			switch(strtolower($postObj->MsgType)){
				case 'event':$this->resEvent($postObj);break;
				case 'text':$this->resText($postObj);break;
				case 'voice':$this->resVoice($postObj);break;
				case 'image':$this->resImage($postObj);break;
				case 'video':$this->resVideo($postObj);break;
				case 'shortvideo':$this->resShortVideo($postObj);break;
				case 'location':$this->resLocation($postObj);break;
				case 'link':$this->resLink($postObj);break;
			}
		
	}//reponseMsg end
	
	

	
	//1、接受文本消息数据包
	/*<xml>
		<ToUserName><![CDATA[toUser]]></ToUserName>
		<FromUserName><![CDATA[fromUser]]></FromUserName>
		<CreateTime>1348831860</CreateTime>
		<MsgType><![CDATA[text]]></MsgType>
		<Content><![CDATA[this is a test]]></Content>
		<MsgId>1234567890123456</MsgId>
	</xml>*/
	function resText($postObj){			
	    switch(trim($postObj->Content) ){
		   	case '回复纯文本消息':
				  $content = '您要求回复纯文本消息';
				  $fromUser = $postObj->ToUserName;
				  $toUser   = $postObj->FromUserName;
				  Response::getInstance()->fetchText($toUser, $fromUser, $content);				  
				  break;	
		    case '回复图片消息':
				  	$media_id = "";
				  	$fromUser = $postObj->ToUserName;
				  	$toUser   = $postObj->FromUserName;
				  	Response::getInstance()->fetchImage($toUser, $fromUser, $media_id);
				  	break;
		    case '回复语音消息':
			  	  	$media_id = "";
				  	$fromUser = $postObj->ToUserName;
				  	$toUser   = $postObj->FromUserName;
				  	Response::getInstance()->fetchImage($toUser, $fromUser, $media_id);
				  	break;
	    	case '回复视频消息':
			  	 	$media_id = "";
				  	$fromUser = $postObj->ToUserName;
				  	$toUser   = $postObj->FromUserName;
				  	$title = "招新视频";
				  	$description = "2016高校开学社团招新视频";
				  	Response::getInstance()->fetchVideo($toUser, $fromUser, $media_id,$title,$description);
				  	break;
				  	break;
		  	case '回复音乐消息':
			  		$fromUser = $postObj->ToUserName;
			  		$toUser   = $postObj->FromUserName;
			  		$title = " 招新音乐";
			  		$description = "2016高校开学社团招新音乐";
			  		$musicUrl = "";
			  		$HQMusicUrl = "";
			  		$ThumbMediaId = "";
			  		Response::getInstance()->fetchMusic($toUser, $fromUser, $title,$description,$musicUrl, $HQMusicUrl, $ThumbMediaId);
			  		break;
			case "回复图文消息":
				$content = array(
								array(
										'title'=>'imooc',
										'description'=>"imooc is very cool",
										'picUrl'=>'http://www.imooc.com/static/img/common/logo.png',
										'url'=>'http://www.imooc.com',
								),
								array(
										'title'=>'hao123',
										'description'=>"hao123 is very cool",
										'picUrl'=>'https://www.baidu.com/img/bdlogo.png',
										'url'=>'http://www.hao123.com',
								),
								array(
										'title'=>'qq',
										'description'=>"qq is very cool",
										'picUrl'=>'http://www.imooc.com/static/img/common/logo.png',
										'url'=>'http://www.qq.com',
								)
				            );
				$fromUser = $postObj->ToUserName;
				$toUser   = $postObj->FromUserName;
				Response::getInstance()->fetchNews($toUser, $fromUser, $content);
			    break;
			default:
				$content = "无法识别您的输入";
				$fromUser = $postObj->ToUserName;
				$toUser   = $postObj->FromUserName;
				Response::getInstance()->fetchText($toUser, $fromUser, $content);
				break;
		}
	}
	
    
	//2、接受图片消息数据包
	/* <xml>
		 <ToUserName><![CDATA[toUser]]></ToUserName>
		 <FromUserName><![CDATA[fromUser]]></FromUserName>
		 <CreateTime>1348831860</CreateTime>
		 <MsgType><![CDATA[image]]></MsgType>
		 <PicUrl><![CDATA[this is a url]]></PicUrl>
		 <MediaId><![CDATA[media_id]]></MediaId>
		 <MsgId>1234567890123456</MsgId>
	 </xml>*/
	function resImage($postObj){
		$fromUser = $postObj->ToUserName;
		$toUser   = $postObj->FromUserName;
		$content = "已收到您的图片消息";
		Response::getInstance()->fetchText($toUser, $fromUser, $content);
	}

	
	//3、接受语音消息数据包
	/*<xml>
		<ToUserName><![CDATA[toUser]]></ToUserName>
		<FromUserName><![CDATA[fromUser]]></FromUserName>
		<CreateTime>1357290913</CreateTime>
		<MsgType><![CDATA[voice]]></MsgType>
		<MediaId><![CDATA[media_id]]></MediaId>
		<Format><![CDATA[Format]]></Format>
		<MsgId>1234567890123456</MsgId>
	</xml>*/
	function resVoice($postObj){
		$fromUser = $postObj->ToUserName;
		$toUser   = $postObj->FromUserName;
		$content = "已收到您的语音消息";
		Response::getInstance()->fetchText($toUser, $fromUser, $content);
	}
	
	//4、接受视频消息数据包
	/*<xml>
		<ToUserName><![CDATA[toUser]]></ToUserName>
		<FromUserName><![CDATA[fromUser]]></FromUserName>
		<CreateTime>1357290913</CreateTime>
		<MsgType><![CDATA[video]]></MsgType>
		<MediaId><![CDATA[media_id]]></MediaId>
		<ThumbMediaId><![CDATA[thumb_media_id]]></ThumbMediaId>
		<MsgId>1234567890123456</MsgId>
	</xml>*/
	function resVideo($postObj){
		$fromUser = $postObj->ToUserName;
		$toUser   = $postObj->FromUserName;
		$content = "已收到您的视频消息";
		Response::getInstance()->fetchText($toUser, $fromUser, $content);
	}

	
	//5、接受短视频消息数据包
	/*<xml>
		<ToUserName><![CDATA[toUser]]></ToUserName>
		<FromUserName><![CDATA[fromUser]]></FromUserName>
		<CreateTime>1357290913</CreateTime>
		<MsgType><![CDATA[shortvideo]]></MsgType>
		<MediaId><![CDATA[media_id]]></MediaId>
		<ThumbMediaId><![CDATA[thumb_media_id]]></ThumbMediaId>
		<MsgId>1234567890123456</MsgId>
	</xml>*/
	function resShortVideo($postObj){
		$fromUser = $postObj->ToUserName;
		$toUser   = $postObj->FromUserName;
		$content = "已收到您的短视频消息";
		Response::getInstance()->fetchText($toUser, $fromUser, $content);
	}
	
	//6、接收地理位置信息数据包
	/*<xml>
		<ToUserName><![CDATA[toUser]]></ToUserName>
		<FromUserName><![CDATA[fromUser]]></FromUserName>
		<CreateTime>1351776360</CreateTime>
		<MsgType><![CDATA[location]]></MsgType>
		<Location_X>23.134521</Location_X>
		<Location_Y>113.358803</Location_Y>
		<Scale>20</Scale>
		<Label><![CDATA[位置信息]]></Label>
		<MsgId>1234567890123456</MsgId>
	</xml>*/
	function resLocation($postObj){
		$fromUser = $postObj->ToUserName;
		$toUser   = $postObj->FromUserName;
		$content = "已收到您的地理位置消息";
		Response::getInstance()->fetchText($toUser, $fromUser, $content);
	}
	
	//7、接受链接数据包
	/*<xml>
		<ToUserName><![CDATA[toUser]]></ToUserName>
		<FromUserName><![CDATA[fromUser]]></FromUserName>
		<CreateTime>1351776360</CreateTime>
		<MsgType><![CDATA[link]]></MsgType>
		<Title><![CDATA[公众平台官网链接]]></Title>
		<Description><![CDATA[公众平台官网链接]]></Description>
		<Url><![CDATA[url]]></Url>
		<MsgId>1234567890123456</MsgId>
	</xml>*/
	function resLink($postObj){
		$fromUser = $postObj->ToUserName;
		$toUser   = $postObj->FromUserName;
		$content = "已收到您的链接消息";
		Response::getInstance()->fetchText($toUser, $fromUser, $content);
	}
	
	//8、接受事件推送
	function resEvent($postObj){
		file_put_contents("read.txt",$postObj->Event."\n");
		if( strtolower($postObj->Event) == 'subscribe'&& !isset($postObj->Ticket)){//关注/取消关注事件推送
			//回复用户消息(纯文本格式)
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = '欢迎关注我们的微信公众账号';
			Response::getInstance()->fetchText($toUser, $fromUser, $content);
			
		}elseif(strtolower($postObj->Event) == 'subscribe' && $postObj->Ticket){//未关注扫码进入事件推送
			/*<xml><ToUserName><![CDATA[toUser]]></ToUserName>
			 <FromUserName><![CDATA[FromUser]]></FromUserName>
			<CreateTime>123456789</CreateTime>
			<MsgType><![CDATA[event]]></MsgType>
			<Event><![CDATA[subscribe]]></Event>
			<EventKey><![CDATA[qrscene_123123]]></EventKey>
			<Ticket><![CDATA[TICKET]]></Ticket>
			</xml>*/
			//回复用户消息(纯文本格式)
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = '您还未关注我们的公众号，请先关注哦';
			Response::getInstance()->fetchText($toUser, $fromUser, $content);
				
		}elseif(strtolower($postObj->Event) == 'scan'){//已关注扫码进入事件推送
			/*<xml>
			 <ToUserName><![CDATA[toUser]]></ToUserName>
			<FromUserName><![CDATA[FromUser]]></FromUserName>
			<CreateTime>123456789</CreateTime>
			<MsgType><![CDATA[event]]></MsgType>
			<Event><![CDATA[SCAN]]></Event>
			<EventKey><![CDATA[SCENE_VALUE]]></EventKey>
			<Ticket><![CDATA[TICKET]]></Ticket>
			</xml>*/
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = '您已经关注我们的公众号了';
			Response::getInstance()->fetchText($toUser, $fromUser, $content);
				
		}elseif(strtolower($postObj->Event) == 'location'){ //上报地理位置事件推送
			/*<xml>
			 <ToUserName><![CDATA[toUser]]></ToUserName>
			<FromUserName><![CDATA[fromUser]]></FromUserName>
			<CreateTime>123456789</CreateTime>
			<MsgType><![CDATA[event]]></MsgType>
			<Event><![CDATA[LOCATION]]></Event>
			<Latitude>23.137466</Latitude>
			<Longitude>113.352425</Longitude>
			<Precision>119.385040</Precision>
			</xml>*/
			
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = "地理位置：<br>Latitude:{$this->Latitude}<br>Longitude:{$this->Longitude}<br>Precision:{$this->Precision}";
			Response::getInstance()->fetchText($toUser, $fromUser, $content);
				
				
		}elseif(strtolower($postObj->Event) == 'click'){ //点击菜单拉取消息时的事件推送
			/*<xml>
			 <ToUserName><![CDATA[toUser]]></ToUserName>
			<FromUserName><![CDATA[FromUser]]></FromUserName>
			<CreateTime>123456789</CreateTime>
			<MsgType><![CDATA[event]]></MsgType>
			<Event><![CDATA[CLICK]]></Event>
			<EventKey><![CDATA[EVENTKEY]]></EventKey>
			</xml>*/
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = "您点击了菜单以拉取消息";
			Response::getInstance()->fetchText($toUser, $fromUser, $content);
				
		}elseif(strtolower($postObj->Event) == 'view'){ //点击菜单跳转链接时的事件推送
			/*<xml>
			 <ToUserName><![CDATA[toUser]]></ToUserName>
			<FromUserName><![CDATA[FromUser]]></FromUserName>
			<CreateTime>123456789</CreateTime>
			<MsgType><![CDATA[event]]></MsgType>
			<Event><![CDATA[VIEW]]></Event>
			<EventKey><![CDATA[www.qq.com]]></EventKey>
			</xml>*/
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = "您点击了菜单以跳转到{$this->EventKey}";
			Response::getInstance()->fetchText($toUser, $fromUser, $content);
			
		}elseif (strtolower($postObj->Event) == 'scancode_push'){  //扫码推事件的事件推送
			/*<xml><ToUserName><![CDATA[gh_e136c6e50636]]></ToUserName>
			<FromUserName><![CDATA[oMgHVjngRipVsoxg6TuX3vz6glDg]]></FromUserName>
			<CreateTime>1408090502</CreateTime>
			<MsgType><![CDATA[event]]></MsgType>
			<Event><![CDATA[scancode_push]]></Event>
			<EventKey><![CDATA[6]]></EventKey>
			<ScanCodeInfo><ScanType><![CDATA[qrcode]]></ScanType>
			<ScanResult><![CDATA[1]]></ScanResult>
			</ScanCodeInfo>
			</xml>*/
		}elseif(strtolower($postObj->Event) == 'scancode_waitmsg'){ //扫码推事件且弹出“消息接收中”提示框的事件推送
			/*<xml>
			<ToUserName><![CDATA[gh_e136c6e50636]]></ToUserName>
			<FromUserName><![CDATA[oMgHVjngRipVsoxg6TuX3vz6glDg]]></FromUserName>
			<CreateTime>1408090606</CreateTime>
			<MsgType><![CDATA[event]]></MsgType>
			<Event><![CDATA[scancode_waitmsg]]></Event>
			<EventKey><![CDATA[6]]></EventKey>
			<ScanCodeInfo><ScanType><![CDATA[qrcode]]></ScanType>
			<ScanResult><![CDATA[2]]></ScanResult></ScanCodeInfo>
			</xml>*/
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = "扫码推事件且弹出“消息接收中”提示框";
			Response::getInstance()->fetchText($toUser, $fromUser, $content);
			
		}elseif(strtolower($postObj->Event) == 'pic_sysphoto'){ //弹出系统拍照发图的事件推送
			/*<xml>
		   <ToUserName><![CDATA[gh_e136c6e50636]]></ToUserName>
			<FromUserName><![CDATA[oMgHVjngRipVsoxg6TuX3vz6glDg]]></FromUserName>
			<CreateTime>1408090651</CreateTime>
			<MsgType><![CDATA[event]]></MsgType>
			<Event><![CDATA[pic_sysphoto]]></Event>
			<EventKey><![CDATA[6]]></EventKey>
			<SendPicsInfo><Count>1</Count>
			<PicList>
			<item><PicMd5Sum><![CDATA[1b5f7c23b5bf75682a53e7b6d163e185]]></PicMd5Sum></item>
			</PicList>
			</SendPicsInfo>
			</xml>*/
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = "弹出系统拍照发图";
			Response::getInstance()->fetchText($toUser, $fromUser, $content);
				
		}elseif(strtolower($postObj->Event) == 'pic_photo_or_album'){ //弹出系统拍照发图的事件推送
			/*<xml>
			<ToUserName><![CDATA[gh_e136c6e50636]]></ToUserName>
			<FromUserName><![CDATA[oMgHVjngRipVsoxg6TuX3vz6glDg]]></FromUserName>
			<CreateTime>1408090816</CreateTime>
			<MsgType><![CDATA[event]]></MsgType>
			<Event><![CDATA[pic_photo_or_album]]></Event>
			<EventKey><![CDATA[6]]></EventKey>
			<SendPicsInfo><Count>1</Count>
			<PicList><item><PicMd5Sum><![CDATA[5a75aaca956d97be686719218f275c6b]]></PicMd5Sum>
			</item>
			</PicList>
			</SendPicsInfo>
			</xml>*/
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = "弹出系统拍照发图";
			Response::getInstance()->fetchText($toUser, $fromUser, $content);
				
		}elseif(strtolower($postObj->Event) == 'pic_weixin'){ //弹出微信相册发图器的事件推送
			/*<xml>
			<ToUserName><![CDATA[gh_e136c6e50636]]></ToUserName>
			<FromUserName><![CDATA[oMgHVjngRipVsoxg6TuX3vz6glDg]]></FromUserName>
			<CreateTime>1408090816</CreateTime>
			<MsgType><![CDATA[event]]></MsgType>
			<Event><![CDATA[pic_weixin]]></Event>
			<EventKey><![CDATA[6]]></EventKey>
			<SendPicsInfo><Count>1</Count>
			<PicList>
			<item><PicMd5Sum><![CDATA[5a75aaca956d97be686719218f275c6b]]></PicMd5Sum></item>
			</PicList>
			</SendPicsInfo>
			</xml>*/
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = "弹出微信相册发图器";
			Response::getInstance()->fetchText($toUser, $fromUser, $content);
				
		}elseif(strtolower($postObj->Event) == 'location_select'){ //弹出地理位置选择器的事件推送
			/*<xml>
			<ToUserName><![CDATA[gh_e136c6e50636]]></ToUserName>
			<FromUserName><![CDATA[oMgHVjngRipVsoxg6TuX3vz6glDg]]></FromUserName>
			<CreateTime>1408091189</CreateTime>
			<MsgType><![CDATA[event]]></MsgType>
			<Event><![CDATA[location_select]]></Event>
			<EventKey><![CDATA[6]]></EventKey>
			<SendLocationInfo><Location_X><![CDATA[23]]></Location_X>
			<Location_Y><![CDATA[113]]></Location_Y>
			<Scale><![CDATA[15]]></Scale>
			<Label><![CDATA[ 广州市海珠区客村艺苑路 106号]]></Label>
			<Poiname><![CDATA[]]></Poiname>
			</SendLocationInfo>
			</xml>*/
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = "弹出地理位置选择器";
			Response::getInstance()->fetchText($toUser, $fromUser, $content);	
						
		}elseif(strtolower($postObj->Event) == 'qualification_verify_success'){//资质认证成功事件
			/*<xml><ToUserName><![CDATA[toUser]]></ToUserName>
			<FromUserName><![CDATA[FromUser]]></FromUserName>
			<CreateTime>1442401156</CreateTime>
			<MsgType><![CDATA[event]]></MsgType>
			<Event><![CDATA[qualification_verify_success]]></Event>
			<ExpiredTime>1442401156</ExpiredTime>
			</xml>*/
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = "您的资质认证成功";
			Response::getInstance()->fetchText($toUser, $fromUser, $content);
			
		}elseif(strtolower($postObj->Event) == 'qualification_verify_fail'){//资质认证失败事件
			/*<xml><ToUserName><![CDATA[toUser]]></ToUserName>
			<FromUserName><![CDATA[FromUser]]></FromUserName>
			<CreateTime>1442401156</CreateTime>
			<MsgType><![CDATA[event]]></MsgType>
			<Event><![CDATA[qualification_verify_fail]]></Event>
			<FailTime>1442401122</FailTime>
			<FailReason><![CDATA[by time]]></FailReason>
			</xml>*/
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = "您的资质认证失败";
			Response::getInstance()->fetchText($toUser, $fromUser, $content);
			
		}elseif(strtolower($postObj->Event) == 'naming_verify_success'){//名称认证成功事件
			/*<xml><ToUserName><![CDATA[toUser]]></ToUserName>
			<FromUserName><![CDATA[FromUser]]></FromUserName>
			<CreateTime>1442401093</CreateTime>
			<MsgType><![CDATA[event]]></MsgType>
			<Event><![CDATA[naming_verify_success]]></Event>
			<ExpiredTime>1442401093</ExpiredTime>
			</xml>*/
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = "您的名称认证成功";
			Response::getInstance()->fetchText($toUser, $fromUser, $content);
			
		}elseif(strtolower($postObj->Event) == 'naming_verify_fail'){//名称认证失败事件
			/*<xml><ToUserName><![CDATA[toUser]]></ToUserName>
			<FromUserName><![CDATA[FromUser]]></FromUserName>
			<CreateTime>1442401061</CreateTime>
			<MsgType><![CDATA[event]]></MsgType>
			<Event><![CDATA[naming_verify_fail]]></Event>
			<FailTime>1442401061</FailTime>
			<FailReason><![CDATA[by time]]></FailReason>
			</xml>*/
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = "您的名称认证失败";
			Response::getInstance()->fetchText($toUser, $fromUser, $content);
			
		}elseif(strtolower($postObj->Event) == 'annual_renew'){//年审通知事件
			/*<xml><ToUserName><![CDATA[toUser]]></ToUserName>
			<FromUserName><![CDATA[FromUser]]></FromUserName>
			<CreateTime>1442401004</CreateTime>
			<MsgType><![CDATA[event]]></MsgType>
			<Event><![CDATA[annual_renew]]></Event>
			<ExpiredTime>1442401004</ExpiredTime>
			</xml>*/
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = "年审通知";
			Response::getInstance()->fetchText($toUser, $fromUser, $content);
			
		}elseif(strtolower($postObj->Event) == 'verify_expired'){//认证过期失效通知事件
			/*<xml><ToUserName><![CDATA[toUser]]></ToUserName>
			<FromUserName><![CDATA[FromUser]]></FromUserName>
			<CreateTime>1442400900</CreateTime>
			<MsgType><![CDATA[event]]></MsgType>
			<Event><![CDATA[verify_expired]]></Event>
			<ExpiredTime>1442400900</ExpiredTime>
			</xml>*/
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = "您的认证过期失效";
			Response::getInstance()->fetchText($toUser, $fromUser, $content);
			
		}elseif(strtolower($postObj->Event) == 'MASSSENDJOBFINISH'){//群发结果事件
			/*<xml>
			<ToUserName><![CDATA[gh_3e8adccde292]]></ToUserName>
			<FromUserName><![CDATA[oR5Gjjl_eiZoUpGozMo7dbBJ362A]]></FromUserName>
			<CreateTime>1394524295</CreateTime>
			<MsgType><![CDATA[event]]></MsgType>
			<Event><![CDATA[MASSSENDJOBFINISH]]></Event>
			<MsgID>1988</MsgID>
			<Status><![CDATA[sendsuccess]]></Status>
			<TotalCount>100</TotalCount>
			<FilterCount>80</FilterCount>
			<SentCount>75</SentCount>
			<ErrorCount>5</ErrorCount>
			</xml>*/
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = "群发结果";
			Response::getInstance()->fetchText($toUser, $fromUser, $content);
			
		}elseif(strtolower($postObj->Event) == 'TEMPLATESENDJOBFINISH' && strtolower($postObj->Status) == 'success'){//模板消息发送成功事件
		   /*<xml>
           <ToUserName><![CDATA[gh_7f083739789a]]></ToUserName>
           <FromUserName><![CDATA[oia2TjuEGTNoeX76QEjQNrcURxG8]]></FromUserName>
           <CreateTime>1395658920</CreateTime>
           <MsgType><![CDATA[event]]></MsgType>
           <Event><![CDATA[TEMPLATESENDJOBFINISH]]></Event>
           <MsgID>200163836</MsgID>
           <Status><![CDATA[success]]></Status>
           </xml>*/
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = "模板消息发送成功";
			Response::getInstance()->fetchText($toUser, $fromUser, $content);
			
		}elseif(strtolower($postObj->Event) == 'TEMPLATESENDJOBFINISH' && strtolower($postObj->Status) == 'failed:user block'){//模板消息发送失败事件（用于用户拒收）
		    /*<xml>
           <ToUserName><![CDATA[gh_7f083739789a]]></ToUserName>
           <FromUserName><![CDATA[oia2TjuEGTNoeX76QEjQNrcURxG8]]></FromUserName>
           <CreateTime>1395658984</CreateTime>
           <MsgType><![CDATA[event]]></MsgType>
           <Event><![CDATA[TEMPLATESENDJOBFINISH]]></Event>
           <MsgID>200163840</MsgID>
           <Status><![CDATA[failed:user block]]></Status>
           </xml>*/
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = "模板消息被拒收";
			Response::getInstance()->fetchText($toUser, $fromUser, $content);
			
		}elseif(strtolower($postObj->Event) == 'TEMPLATESENDJOBFINISH' && strtolower($postObj->Status) == 'failed: system failed'){//模板消息发送失败事件（用于用户拒收）
		   /*<xml>
           <ToUserName><![CDATA[gh_7f083739789a]]></ToUserName>
           <FromUserName><![CDATA[oia2TjuEGTNoeX76QEjQNrcURxG8]]></FromUserName>
           <CreateTime>1395658984</CreateTime>
           <MsgType><![CDATA[event]]></MsgType>
           <Event><![CDATA[TEMPLATESENDJOBFINISH]]></Event>
           <MsgID>200163840</MsgID>
           <Status><![CDATA[failed: system failed]]></Status>
           </xml>*/
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$content  = "系统原因导致模板消息发送失败";
			Response::getInstance()->fetchText($toUser, $fromUser, $content);
			
		}
		
	}
		
}