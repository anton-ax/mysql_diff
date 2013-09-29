<?php 
class Dumper {

	private $host;
	private $user;
	private $pass;
	private $db;
	private $port;
	public $tables;

	public function __construct($from){
		$m = preg_match('/^(\S+):(\S+)@([\w|\d|\.|-]+):(\d+)?\/(\w+)$/', $from, $matches);
		$this->host = $matches[3];
		$this->port = $matches[4];
		$this->user = $matches[1];
		$this->pass = $matches[2];
		$this->db = $matches[5];
		$this->tables = array();
		if (!$m) return NULL;
	}

	public function dump(){
		$link = new mysqli(
			$this->host,
			$this->user,
			$this->pass,
			$this->db,
			(int) $this->port
			);
			$link->set_charset('utf8');

		$result = $link->query('SHOW TABLE STATUS FROM '.$this->db);
		if($result) {	
			while ($table = $result->fetch_assoc()) {
			   $columns = $link->query('SHOW FULL COLUMNS FROM '.$table['Name']);
			   $column = array();
			   if ($columns){
				   while ($row = $columns->fetch_assoc()) {
				   	 $column[$row['Field']] = $row;
				   }
				}
			   $this->tables[$table['Name']] = array(
			   					"engine"  => $table['Engine'], 
			   					"collate"  => $table['Collation'], 
			   					"columns"  =>  $column);
			}
		}
		return $this->tables;
	}
}