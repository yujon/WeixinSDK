<?php
namespace Controls;

class User extends Common{
   
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
	
	//创建分组
	function createGroup($name){    //{"group":{"name":"test"}}
		$access_token = $this->getWxAccessToken();
		$url="https://api.weixin.qq.com/cgi-bin/groups/create?access_token={$access_token}";
		$arr = array("group"=>array("name"=>$name));
		$data = json_encode($arr);
        $res = $this->http_curl($url,true,true,$data);
        if(isset($res->errcode)){
        	echo $res->errmsg;
			return false;
        }
        return $res;
	}

    //获取所有分组
	function getGroups(){
		$access_token = $this->getAccessToken();
		$url="https://api.weixin.qq.com/cgi-bin/groups/get?access_token={$access_token}";
		$res = http_curl($url);
		if(isset($res->errcode)){
			echo $res->errmsg;
			return false;
		}
		return $res;
	}
	
	//删除分组
	function deleteGroup($id){
		$access_token = $this->getAccessToken();
		$url="https://api.weixin.qq.com/cgi-bin/groups/delete?access_token={$access_token}";
		$arr = array("group"=>array("id"=>$id));
		$data = json_encode($arr);
        $res = http_curl($url,true,true,$data);
        if($res->errcode){
        	echo $res->errmsg;
			return false;
        }
        return $res;
	}
	
	//修改分组名
	function updateGroup($id,$name){
		$access_token = $this->getAccessToken();
		$url="https://api.weixin.qq.com/cgi-bin/groups/update?access_token={$access_token}";
		$arr = array("group"=>array("id"=>$id,"name"=>$name));
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		if($res->errcode){
			echo $res->errmsg;
			return false;
		}
		return $res;
	}
	
	//获取用户所属分组
	function getUserFrom($openid,$to_groupid){
		$access_token = $this->getAccessToken();
		$url="https://api.weixin.qq.com/cgi-bin/groups/getid?access_token={$access_token}";
		$arr = array("group"=>array("openid"=>$openid));
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		if($res->errcode){
			echo $res->errmsg;
			return false;
		}
		return $res;
	}
	
	//移动用户到某个分组
	function moveUserTo($idarr,$to_groupid){
		$access_token = $this->getAccessToken();
		$url="https://api.weixin.qq.com/cgi-bin/groups/members/batchupdate?access_token?access_token={$access_token}";
		$arr = array("group"=>array("openid"=>$openid));
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		if($res->errcode){
			echo $res->errmsg;
			return false;
		}
		return $res;
	}
	
	//批量移动用户
	function moveUsersTo($agrs){
		$access_token = $this->getAccessToken();
		$url="https://api.weixin.qq.com/cgi-bin/groups/members/batchupdate?access_token={$access_token}";
		$arr = array();
		foreach ($agrs as $key=>$val){
			$arr['openid_list'][]=$val;
		}		
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		if($res->errcode){
			echo $res->errmsg;
			return false;
		}
		return $res;
	}
	
	//设置用户备注名
	function setName($openid,$remark){
		$access_token = $this->getAccessToken();
		$url="https://api.weixin.qq.com/cgi-bin/user/info/updateremark?access_token={$access_token}";
		$arr = array("openid"=>$openid,"remark"=>$remark);
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		if(isset($res->errcode)){
			echo $res->errmsg;
			return false;
		}
		return $res;
	}
    
	//获取用户基本信息
	function getUserInfo($openid,$lang="zh_CN"){
		$access_token = $this->getAccessToken();
		$url="https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$openid}&lang={$lang}";
		$res = $this->http_curl($url,true);
		if(isset($res->errcode)){
			echo $res->errmsg;
			return false;
		}
		return $res;
	}
	
	//批量获取用户基本信息
	function getUsersInfo($openids,$langs="zh_CN"){
		$access_token = $this->getAccessToken();
		$url="https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token={$access_token}";
		$arr = array();
		foreach ($openids as $key=>$openid){
			$arr['user_list'][$key]['openid']=$openid;
			$arr['user_list'][$key]['lang']=$langs;
		}
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		if(isset($res->errcode)){
			echo $res->errmsg;
			return false;
		}
		return $res;
	}
	
	//获取用户列表
	function getUserList($nextOpenid){
		$access_token = $this->getAccessToken();
		$url="https://api.weixin.qq.com/cgi-bin/user/get?access_token={$access_token}&next_openid={$nextOpenid}";
		$res = $this->http_curl($url,true);
		print_r($res);
		exit;
		if(isset($res->errcode)){
			echo $res->errmsg;
			return false;
		}
		return $res;
	}
	
	
	
	//网页授权
	function getBaseInfo($type=2){  //1为获取openid,2为获取用户信息
		//1,用户同意授权，获取code
		if($type == 1){
			$redirect_uri = urlencode("http://15456cr684.51mypc.cn/github/eclipse/weixinceshi/api/index.php/User/getOpenid");
			$scope = "snsapi_base";
		}else{
			$redirect_uri = urlencode("http://15456cr684.51mypc.cn/github/eclipse/weixinceshi/api/index.php/User/getInfo");
			$scope = "snsapi_userinfo";
		}
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->appid}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}&state=123#wechat_redirec";
		header("location:".$url);
	}
	
	//获取用户openid
	function getOpenid(){
		//2 第二步：通过code换取openid
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->appid}&secret={$this->appsecret}&code={$_GET['code']}&grant_type=authorization_code";
		$res = $this->http_curl($url,true);		
		if(isset($res->errcode)){
			print_r($res);
			return false;
		}
		return $res->openid;
		
	}
	
	//获取用户信息
	function getInfo(){
		//2 第二步：通过code换取网页授权access_token
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->appid}&secret={$this->appsecret}&code={$_GET['code']}&grant_type=authorization_code";
		$res = $this->http_curl($url,true);
		if(isset($res->errcode)){
			print_r($res);
			return false;
		}
		//3 刷新access_token（如果需要）
		if(!$this->isvaild($res->access_token, $res->openid)){
			$url = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid={$this->appid}&grant_type=refresh_token&refresh_token={$res->refresh_token}";
			$res = $this->http_curl($url,true);
			if(isset($res->errcode)){
				print_r($res);
				return false;
			}
		}
		//4 第四步：拉取用户信息(需scope为 snsapi_userinfo)
		$url="https://api.weixin.qq.com/sns/userinfo?access_token={$res->access_token}&openid={$res->openid}&lang=zh_CN";
		$res = $this->http_curl($url,true);
		if(isset($res->errcode)){
			print_r($res);
			return false;
		}
//      print_r($res);
		return $res;
	}
	
	//检验授权凭证是否有效
	function isvaild($access_token,$openid){
		$url = "https://api.weixin.qq.com/sns/auth?access_token={$access_token}&openid={$openid}";
		$res = $this->http_curl($url,true);
		if($res->errcode){
			return false;
		}
		return true;
	}
	
	
}
