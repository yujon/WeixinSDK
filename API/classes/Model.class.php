<?php
//所有模型类的基类，封装了一些常用的数据库操作函数
	namespace Classes;
// 	require_once "Autoloader.class.php";
// 	spl_autoload_register("\Classes\\Autoloader::autoload");
	
	class Model{
		
		public $db;
		public $dbPrev;
		public $where_cond='';
		public $limit_cond = ""; 
		public $order_cond = "";
		public $field_cond = "*";
		public $tName;  //表名
		public $fieldList;  //字段列表
		
		//构造函数
		protected  function __construct(){  //默认构建第一条记录为模型数据	
			$configs =\Classes\Configs::getInstance();
			@$db_config = $configs->offsetGet('db');
			$Pdo = Pdo::getInstance($db_config);
			$this->db = $Pdo->pdo;
			$this->dbPrev = $db_config['dbPrev'];
			$arr = explode("\\",get_class($this));
			$this->tName = strtolower(array_pop($arr));
			$sql = "select column_name from information_schema.COLUMNS where table_schema='{$db_config['dbName']}' and table_name='{$this->dbPrev}{$this->tName}'";
			$res = $this->db->query($sql);
			if($res){
				$data= $res->fetchAll(\PDO::FETCH_ASSOC);
				for($i=0;$i<count($data);$i++){
					$this->fieldList[] = $data[$i]["column_name"];
				}	 
			}else{
				return "查无字段";
			}
		}
		
		//重置属性
		function resetInfo(){
			$this->where_cond='';
			$this->limit_cond = "";
			$this->order_cond = "";
			$this->field_cond = "*";

		}
				
		//filter = 1 去除 " ' 和 HTML 实体， 0则不变
		private function check($array, $filter){
			$arr=array();		
			foreach($array as $key=>$value){
				$key=strtolower($key);
				if(in_array($key, $this->fieldList) && $value !== ''){
					if(is_array($filter) && !empty($filter)){
						if(in_array($key, $filter)){
							$arr[$key]=$value;
						}else{
							$arr[$key]=stripslashes(htmlspecialchars($value));
						}
					}else if(!$filter) {
						$arr[$key]=$value;
					}else{
						$arr[$key]=stripslashes(htmlspecialchars($value));
					}
				}
			}
			return $arr;
		}

		//设置操作的条件
		function where($arg = -1){
			$res = "";
			$arg_num = func_num_args();
			if($arg_num>1){  //如果多个参数，必须每个都为数组
				$args = func_get_args();
				for($i=0;$i<$arg_num;$i++){
					if(is_array($args[$i])){
						$cond = "(";
						$keys = array_keys($args[$i]);
						$vals = array_values($args[$i]);
						for($j=0;$j<count($args[$i]);$j++){
							if($keys[$j] == "password"){
								$vals[$j] = md5($vals[$j]);
							}
							if($j == count($args[$i])-1){
								$cond = $cond.$keys[$j]."='".$vals[$j]."'";
							}else{
								$cond = $cond. $keys[$j]."='".$vals[$j]."' and ";
							}
						}
						$cond = $cond.")";
					}
					if($i < $arg_num - 1){
						$res = $res.$cond." or ";
					}else{
						$res = $res.$cond;
					}
				}
				$this->where_cond = " where ".$res;			
			}else{
				if($arg == -1){
					$this->where_cond = " where id = '1'";
				}else if(is_array($arg)){
					$cond = '';
					$keys = array_keys($arg);
					$vals = array_values($arg);
					for($i=0;$i<count($arg);$i++){
						if($keys[$i] == "password"){
							$vals[$i] = md5($vals[$i]);
						}
						if($i == count($arg)-1){
							$cond = $cond.$keys[$i]."='".$vals[$i]."'";
						}else{
							$cond = $cond. $keys[$i]."='".$vals[$i]."' and ";
						}
					}
					$this->where_cond = " where ".$cond;
				}else if(is_numeric($arg) && $arg>0){
					$this->where_cond = " where id='{$arg}'";
				}else if(is_string($arg)){
					$this->where_cond = " where ".$arg;
				}else{
					throw new \Exception("参数格式有误");
				}
			}
			return $this;
		}
		
		//设置查询的条数
		function limit($offset = 0,$num = -1){
			if($num == -1&&$offset>0){
				$this->limit_cond = " limit ".$offset;
			}elseif($num == -1&&$offset==0){
				$this->limit_cond = "";
			}else{
				$offset--;
				$this->limit_cond = " limit {$offset},{$num}";;
			}
			return $this;
		}
		
		//设置查询结果排练顺序
		function order($str=""){
			if($str!=""){
				$this->order_cond = " order by {$str} ";
			}
			return $this;
		}
		
		//设置查询的字段
		function field($args=""){
			if($args!=""){
				if(is_string($args)){
					$arr = preg_replace("/(\s)+/", ",", $args);
					$arr = explode(",", $arr);
				}elseif(is_array($args)){
					$arr = $args;
				}	
				for ($i=0;$i<count($arr);$i++){
					if(!in_array($arr[$i], $this->fieldList)){
						unset($arr[$i]);
					}
				}
				$this->field_cond = implode(",", $arr);
			}
			return $this;
		}
		
		//按条件查询记录,返回查询到的所有记录
		function select(){
			$sql = "select ".$this->field_cond." from ".$this->dbPrev.$this->tName.$this->where_cond.$this->order_cond.$this->limit_cond;
			$res = $this->db->query($sql);
			if($res){
				$data = $res->fetchAll(\PDO::FETCH_ASSOC);
			}else{
				return "查无记录";
			}
			return $data;		
		}
		
		//按条件查询记录,返回查询到的第一条记录
		function find(){
			$sql = "select ".$this->field_cond." from ".$this->dbPrev.$this->tName.$this->where_cond.$this->order_cond.$this->limit_cond;
			$res = $this->db->query($sql);
			if($res){
				$data = $res->fetch(\PDO::FETCH_ASSOC);
			}else{
				return "查无记录";
			}
			return $data;
		}
		//按条件查询返回符合的记录条数
		function total(){
			$sql = "select COUNT(*) as count FROM ".$this->dbPrev.$this->tName. $this->where_cond;
			$res = $this->db->query($sql);
			$data = $res->fetch(\PDO::FETCH_ASSOC);
			return $data['count'];
		}
		//修改记录
		function update($data,$filter=0){
// 			$data = $this->check($data, $filter);
			$sql = "update ".$this->dbPrev.$this->tName." set ";
			if(is_array($data)){
				foreach($data as $key=>$val){
					if($key!="id"){
						if($key != "password"){
							$key_val[] = $key."='".$val."'";
						}else{
							$key_val[] = $key."='".md5($val)."'";
						}
						
					}			
				}
				$sql = $sql.implode(",", $key_val);
			}else if(is_string($data)){
				$sql = $sql.$data;
			}
			$sql = $sql.$this->where_cond;
			if(!$this->db->query($sql)){
				throw new \Exception("修改数据失败");
			}
			return true;
		}
	
		//添加记录
		function insert($data,$filter){
// 			$data = $this->check($data, $filter);
			$sql = "insert into ".$this->dbPrev.$this->tName;
			foreach ($data as $key=>$val){
				if($key!="id"){
					$keys[]=$key;
					if($key != "password"){
						$vals[]="'".$val."'";
					}else{
						$vals[]="'".md5($val)."'";
					}					
				}
			}
			$sql = $sql."(".implode(",", $keys).") values(".implode(",", $vals).")";
			if(!$this->db->query($sql)){
				throw new \Exception("插入数据失败");
			}
			return true;
		}
		
		//删除记录
		function delete(){
			$sql = "delete from ".$this->dbPrev.$this->tName.$this->where_cond;
			if(!$this->db->query($sql)){
				throw new \Exception("插入或删除数据失败");
			}
			return true;
		}
		
		//构造函数
		function __destruct(){
		}
		
	}
