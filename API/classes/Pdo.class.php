<?php
//数据库连接操作类Pdo
namespace Classes;
// require_once "classes/Autoloader.class.php";
// spl_autoload_register("\Classes\\Autoloader::autoload");

class Pdo{
	private $dsn;	
	private $username;
	private $password;
	protected static $instance;
	public  $pdo;
	
	//够造函数
	private function __construct($db_config){
			$this->dsn = "mysql:host={$db_config["host"]};dbname={$db_config["dbName"]};";
			$this->username = $db_config["dbUsername"];
			$this->password = $db_config["dbPassword"];
			try{
				$this->pdo = new \PDO($this->dsn, $this->username, $this->password);
			}catch(PDOException $e)
			{
				echo $e->getMessage();
			}
			$this->pdo->query('set names utf8');
			$this->pdo->setAttribute(\PDO::ATTR_CASE,\PDO::CASE_LOWER);
	}	
	
	//防止对象被复制
	private function __clone(){
		trigger_error('Clone is not allowed !');
	}
	
	//单例模式
	public static function getInstance($db_config){
		if(!(self::$instance instanceof self)){
			self::$instance = new Pdo($db_config);
		}
		return self::$instance;;
	}
	
	//获取数据表里的字段
	public function getFields($table)
	{
		$this->sth = $this->pdo->query("DESCRIBE $table");
		$this->getPDOError();
		$this->sth->setFetchMode(PDO::FETCH_ASSOC);
		$result = $this->sth->fetchAll();
		$this->sth = null;
		return $result;
	}
	
	//获取所有数据表
	public function getAllTable(){
		$sql = "show tables";
		$rows = $this->doSql($sql);
		$tables = array();
		foreach ($rows as $row){
			$items = array();
			$table = $row['Tables_in_'.self::DB_NAME];
			$sql = "show columns from  {$table}";
			$items = $this->doSql($sql);
			$columns = array();
			foreach ($items as $item){
				$columns[]=$item['Field'];
			}
			$tables[$table]= $columns;
		}
		return $tables;
	}
	
	//获取要操作的数据
	private function getCode($table,$args)
	{
		$allTables = $this->getAllTable();/*返回所有的表及其字段*/
		if (!is_array($allTables[$table]))
		{
			exit('表名错误或未更新缓存!');
		}
		$code = '';
		if (is_array($args))
		{
			foreach ($args as $k => $v)
			{
				if ($v == '')
				{
					continue;
				}
				$code .= "`$k`='$v',";
			}
			}
			$code = substr($code,0,-1);
			return $code;
	}
	 
	/**
	* @comment 插入数据
	* @author Wei.li <php.wei.li@gmail.com>
	* @date  2015-4-20上午9:16:37
	* @param string $table  array $args
	* @return  integer id
	*/
	public function insert($table,$args,$debug = null)
	{
		$sql = "INSERT INTO `$table` SET ";
		$code = $this->getCode($table,$args);
		$sql .= $code;
		if ($debug)echo $sql;
		if ($this->pdo->exec($sql))
		{
				$this->getPDOError();
		return $this->pdo->lastInsertId();
		}
		return false;
	}
	 
	//查询数据
	public function fetch($table,$condition = '',$sort = '',$page = '',$field = '*',$debug = false)
	{
		$sql = "SELECT {$field} FROM `{$table}`";
		if (false !== ($con = $this->getCondition($condition))){
			$sql .= $con;
		}
		if ($sort != ''){
		$sql .= " ORDER BY $sort";
		}
		if ($page != ''){
			$page_size = self::page_size;
			$limit = ($page-1)*$page_size;
			$sql .= " LIMIT $limit , $page_size";
		}
		if ($debug)echo $sql;
		$this->sth = $this->pdo->query($sql);
		$this->getPDOError();
		$this->sth->setFetchMode(PDO::FETCH_ASSOC);
		$result = $this->sth->fetchAll();
		$this->sth = null;
		return $result;
	}
		
	//查询数据
	public function fetchOne($table,$condition = null,$field = '*',$debug = false)
	{
		$sql = "SELECT {$field} FROM `{$table}`";
		if (false !== ($con = $this->getCondition($condition)))
		{
			$sql .= $con;
		}
		if ($debug)echo $sql;
		$this->sth = $this->pdo->query($sql);
		$this->getPDOError();
		$this->sth->setFetchMode(PDO::FETCH_ASSOC);
		$result = $this->sth->fetch();
		$this->sth = null;
		return $result;
	}
	
	/**
	* @comment查询数据 返回id
	* @author Wei.li <php.wei.li@gmail.com>
	* @date  2015-4-20上午9:35:16
	* @param string tableName , string condition
	* @return
	*/
	public function fetchId($table,$condition = null,$debug = false){
		$sql = "SELECT id FROM `{$table}`";
		if (false !== ($con = $this->getCondition($condition)))
		{
			$sql .= $con;
		}
		if ($debug)echo $sql;
		$this->sth = $this->pdo->query($sql);
		$this->getPDOError();
		$this->sth->setFetchMode(PDO::FETCH_ASSOC);
		$result = $this->sth->fetch();
		$this->sth = null;
		return $result['id'];
	}

	//获取查询条件
	public function getCondition($condition='')
	{
		if ($condition != '')
		{
			$con = ' WHERE';
			if (is_array($condition))
			{
				$i = 0;
				foreach ($condition as $k => $v)
				{
					if ($i != 0){
						$con .= " AND $k = '$v'";
					}else {
						$con .= " $k = '$v'";
					}
					$i++;
				}
			}elseif (is_string($condition))
			{
	   $con .= " $condition";
			}else {
	   return false;
			}
			return $con;
		}
		return false;
	}
	 
	//获取记录总数
	public function counts($table,$condition = '',$debug = false)
	{
		$sql = "SELECT COUNT(*) AS num FROM `$table`";
		if (false !== ($con = $this->getCondition($condition)))
		{
			$sql .= $con;
		}
		if ($debug) echo $sql;
		$count = $this->pdo->query($sql);
		$this->getPDOError();
		return $count->fetchColumn();
	}

	//按SQL语句查询
	public function doSql($sql,$model='many',$debug = false)
	{
		if ($debug)echo $sql;
		$this->sth = $this->pdo->query($sql);
		$this->getPDOError();
		$this->sth->setFetchMode(PDO::FETCH_ASSOC);
		if ($model == 'many')
		{
			$result = $this->sth->fetchAll();
		}else {
			$result = $this->sth->fetch();
		}
		$this->sth = null;
		return $result;
	}

	//修改数据
	public function update($table,$args,$condition,$debug = null)
	{

		$code = $this->getCode($table,$args);
		$sql = "UPDATE `$table` SET ";
		$sql .= $code;
		if (false !== ($con = $this->getCondition($condition)))
		{
			$sql .= $con;
		}
		if ($debug)echo $sql;
		if (($rows = $this->pdo->exec($sql)) > 0)
		{
			$this->getPDOError();
			return $rows;
		}
		return false;
	}

	//字段递增
	public function increase($table,$condition,$field,$debug=false)
	{
		$sql = "UPDATE `$table` SET $field = $field + 1";
		if (false !== ($con = $this->getCondition($condition))){
			$sql .= $con;
		}
		if ($debug)echo $sql;
		if (($rows = $this->pdo->exec($sql)) > 0){
			$this->getPDOError();
			return $rows;
		}
		return false;
	}
		
	//删除记录
	public function del($table,$condition,$debug = false)
	{
		$sql = "DELETE FROM `$table`";
		if (false !== ($con = $this->getCondition($condition)))
		{
			$sql .= $con;
		}else {
			exit('条件错误!');
		}
		if ($debug)echo $sql;
		if (($rows = $this->pdo->exec($sql)) > 0)
		{
			$this->getPDOError();
			return $rows;
		}else {
			return false;
		}
	}

		
	/**
		* 执行无返回值的SQL查询
		*
		*/
	public function execute($sql)
	{
		$this->pdo->exec($sql);
		$id = $this->pdo->lastInsertId();
		$data = array();
		if($id){
			$data['info'] = '成功';
			$data['status']='T';
			$data['id']=$id;
		}else{
			$err_info = $this->pdo->errorInfo();
			$data['info'] = $err_info[2];
			$data['status']='F';
		}
		return $data;
	}
		
	/**
		* 捕获PDO错误信息
		*/
	private function getPDOError()
	{
		if ($this->pdo->errorCode() != '00000')
		{
			$error = $this->pdo->errorInfo();
			if(strstr($error[2], 'Duplicate entry')){
				$err_info  ='总登记号重复';
			}else{
				$err_info = $error[2];
			}
		}
		return $err_info;
	}

	//关闭连接
    function __destruct(){
    	$this->pdo=null;
    	self::$instance=null;
    }
}



