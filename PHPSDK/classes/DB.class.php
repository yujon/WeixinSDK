<?php
   //工具类，根据参数创建读取某一应用下的模型操作类文件
  
	namespace Classes;
// 	require_once 'Autoloader.class.php';
// 	spl_autoload_register("\Classes\\Autoloader::autoload");
	
	class DB{
		
		static public $model_name;
		static public $models_path;
		
	 	static function getModel($model_name=null,$app='home'){ //$app为homn|mobile|admin
	 		self::$models_path =$app."/models/".$model_name.".class.php";  //以首页为参照
	 		if(file_exists(self::$models_path)){ 			
		 		$abs_path = "\\".ucfirst($app)."\\Models\\".ucfirst($model_name);  //引入模型操场文件并创建对象
		 		$model = $abs_path::getInstance();
		 		return $model;
	 		}else{
	 			throw new \Exception($model_name."操作模型不存在");
	 		}	
	 		 
	 	}
	 	
	}
	