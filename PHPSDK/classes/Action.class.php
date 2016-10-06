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
   
	}
?>