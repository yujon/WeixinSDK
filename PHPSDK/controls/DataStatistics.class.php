<?php
namespace Controls;

class DataStatistics extends Common{
	
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
	
	//用户数据统计
	function userSatistics($type,$begin_date,$end_date){
		if($end_date - $begin_type > 7){
			$log = "时间跨度超过限制";
			log($log);
			return false;
		}
		
		if($type == 1){  //获取用户增减数据
			$searchType="getusersummary";
		}elseif($type == 2){  //获取累计用户数据
			$searchType = "getusercumulate";
		}else{
			$log = "用户统计类型选择错误，应该选择1或者2";
			log($log);
			return false;
		}	
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/datacube/{$searchType}?access_token={$access_token}";
		$arr = array("begin_date"=>$begin_date,"end_date" => $end_date);
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		return $res->list;
	}
	
	//图文数据统计
	function articlesStatistics($type,$begin_date,$end_date){
		switch ($type){
			case 1:  //获取图文群发每日数据
				if($end_date - $begin_date > 1){
					$overLimit = true;
					break;
				}
				$searchType = "getarticlesummary";
				break; 
			case 2:  //获取图文群发总数据
				if($end_date - $begin_date > 1){
					$overLimit = true;
					break;
				}
				$searchType = "getarticletotal";
				break;  
			case 3:  //获取图文统计数据
				if($end_date - $begin_date > 3){
					$overLimit = true;
					break;
				}
				$searchType = "getuserread";
			    break;  
			case 4:   //获取图文统计分时数据
				if($end_date - $begin_date > 1){
					$overLimit = true;
					break;
				}
				$searchType = "getuserreadhour";
				break;
			case 5:   //获取图文分享转发数据
				if($end_date - $begin_date > 7){
					$overLimit = true;
					break;
				}
				$searchType = "getusershare";
				break; 
			case 6:   //获取图文分享转发分时数据
				if($end_date - $begin_date > 1){
					$overLimit = true;
					break;
				}
				$searchType = "getusershare";
				break;
			default: 
				$log = "用户统计类型选择错误";
				log($log);  
				return false;
		}
		
		if($overLimit){
			$log = "日期跨度超过限制";
			log($log);
			return false;
		}
		
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/datacube/{$searchType}?access_token={$access_token}";
		$arr = array("begin_date"=>$begin_date,"end_date" => $end_date);
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		return $res->list;
	}
	
	//消息数据统计
	function newsStatistics($type,$begin_date,$end_date){
		switch ($type){
			case 1:  //获取消息发送概况数据
				if($end_date - $begin_date > 7){
					$overLimit = true;
					break;
				}
				$searchType = "getupstreammsg";
				break;
			case 2:  //获取消息分送分时数据
				if($end_date - $begin_date > 1){
					$overLimit = true;
					break;
				}
				$searchType = "getupstreammsghour";
				break;
			case 3:  //获取消息发送周数据
				if($end_date - $begin_date > 30){
					$overLimit = true;
					break;
				}
				$searchType = "getupstreammsgweek";
				break;
			case 4:   //获取消息发送月数据
				if($end_date - $begin_date > 30){
					$overLimit = true;
					break;
				}
				$searchType = "getupstreammsgmonth";
				break;
			case 5:   //获取消息发送分布数据
				if($end_date - $begin_date > 15){
					$overLimit = true;
					break;
				}
				$searchType = "getupstreammsgdist";
				break;
			case 6:   //获取消息发送分布周数据
				if($end_date - $begin_date > 30){
					$overLimit = true;
					break;
				}
				$searchType = "getupstreammsgdistweek";
				break;
			case 7:   //获取消息发送分布月数据
				if($end_date - $begin_date > 30){
					$overLimit = true;
					break;
				}
				$searchType = "getupstreammsgdistmonth";
				break;
			default:
				$log = "消息统计类型选择错误";
				log($log);
				return false;
		}

		if($overLimit){
			$log = "日期跨度超过限制";
			log($log);
			return false;
		}

		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/datacube/{$searchType}?access_token={$access_token}";
		$arr = array("begin_date"=>$begin_date,"end_date" => $end_date);
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		return $res->list;
	}
	
	//接口调用数据统计，1获取用户增减数据，2获取累计用户数据
	function interfacesSatistics($type,$begin_date,$end_date){
	
		if($type == 1 && ($end_date-$begin_date<=30){  //获取接口分析数据
			$searchType="getinterfacesummary";
		}elseif($type == 2 && ($end_date-$begin_date<=1)){  ////获取接口分析时数据
			$searchType = "getinterfacesummary";
		}else{    
			$log = "用户统计类型选择错误或者时间跨度超过限制";
			log($log);
			return false;
		}
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/datacube/{$searchType}?access_token={$access_token}";
		$arr = array("begin_date"=>$begin_date,"end_date" => $end_date);
		$data = json_encode($arr);
		$res = $this->http_curl($url,true,true,$data);
		return $res->list;
	}
	
}