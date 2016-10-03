<?php 
    //前后台以及移动端所有控制类的基类 
	namespace Classes;
	
	class Action{
		
		
		/**
		 * 该方法用来运行框架中的操制器，在入口文件中调用
		 */
		function run(){
			//如果子类继承了Common类，调用这个类的init()方法 做权限控制
			if(method_exists($this, "init")){			
				$this->init();
			}	
			//根据动作去找对应的方法
			$method=$_GET["a"];
			if(method_exists($this, $method)){
				$this->$method();
			}
// 			else{
// 				echo "没有{$_GET["a"]}这个操作！";
// 			}
		}

		/**
		 * 用于在控制器中进行位置重定向
		 * @param	string	$path	用于设置重定向的位置
		 * @param	string	$args 	用于重定向到新位置后传递参数
		 *
		 * $this->redirect("index")  /当前模块/index
		 * $this->redirect("user/index") /user/index
		 * $this->redirect("user/index", 'page/5') /user/index/page/5
		 */
		function redirect($path, $args=""){
			$path=trim($path, "/");
			if($args!="")
				$args="/".trim($args, "/");
			if(strstr($path, "/")){
				$url=$path.$args;
			}else{
				$url=$_GET["m"]."/".$path.$args;
			}
		
			$uri=B_APP.'/'.$url;
			//使用js跳转前面可以有输出
			echo '<script>';
			echo 'location="'.$uri.'"';
			echo '</script>';
		}
		
	}

?>