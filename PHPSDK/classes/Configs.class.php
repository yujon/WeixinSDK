<?php 
/*
*  工具类
*  根据参数读取配置文件
* configs文件下的配置文件名必须为**.inc.php格式
*/
namespace Classes;

class  Configs implements \ArrayAccess{
    
    protected $dir_path;
    protected $configs = array();
    private static $instance;

    private function __construct(){
    	$this->dir_path = $GLOBALS['root'];
    }
    
    
    //单例模式
    public static function getInstance(){
    	if(!(self::$instance instanceof self)){
    		self::$instance = new self();
    	}
    	return self::$instance;
    }
    
    function offsetExists($filename){
        return isset($this->configs[$filename]);
    }

    function offsetUnset($filename){
    	unset($this->configs[$filename]);
    }

    function offsetGet($filename,$filedir="configs"){  //所要获取的文件名
    	if(empty($this->configs[$filename])){
    		$file_path ="{$this->dir_path}/{$filedir}/{$filename}.inc.php";
			$this->configs[$filename] = require_once $file_path;
    	}
    	return $this->configs[$filename];
    }
   
    //写入配置文件
    function offsetSet($filename,$data,$filedir="configs",$flags=false){
        // 如果文件不存在，file_put_contents 函数会自动创建文件；如果文件已存在，原有文件被重写。
        // 如果你想在一个已有文件上追加内容，只需要加一个参数。
    	$content = "<?php\n\r\$arr=";
        if(is_array($data) || is_object($data)){
        	$content =  $content.var_export($data,true);
        }else if(is_string($data)){
        	$content .= $data;
        }else{
        	writeLog("{$filename}配置文件写入失败");
        	return false;
        }
        $content .= ";\r\nreturn \$arr;";
        
        //修正文件路径
        $file_path ="{$this->dir_path}/{$filedir}/{$filename}.inc.php";
     
             
        if($flags){ //追加或者重写
        	file_put_contents($file_path, $content,FILE_APPEND);
        }else{
        	file_put_contents($file_path, $content);
        }   	
        if(file_exists($filename)){
            return true;
        }
        return false;
    }
}

 ?>
