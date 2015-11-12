<?php
/*DON'T BE A DICK PUBLIC LICENSE

Everyone is permitted to copy and distribute verbatim or modified copies of this license document, and changing it is allowed as long as the name is changed.

	DON'T BE A DICK PUBLIC LICENSE TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

	Do whatever you like with the original work, just don't be a dick.

	Being a dick includes - but is not limited to - the following instances:

	1a. Outright copyright infringement - Don't just copy this and change the name.
	1b. Selling the unmodified original with no work done what-so-ever, that's REALLY being a dick.
	1c. Modifying the original work to contain hidden harmful content. That would make you a PROPER dick.

	If you become rich through modifications, related works/services, or supporting the original work, share the love. Only a dick would make loads off this work and not buy the original works creator(s) a pint.

	Code is provided with no warranty. Using somebody else's code and bitching when it goes wrong makes you a DONKEY dick. Fix the problem yourself. A non-dick would submit the fix back.
*/
if(!defined('MTG_ENABLE'))
	exit;
require_once DIRNAME(__DIR__) . '/config.php';
class database {
	protected $last_query;
	protected $conn;
	private $host = DB_HOST;
	private $user = DB_USER;
	private $pass = DB_PASS;
	private $name = DB_NAME;
	private $db;
	private $stmt;
	static $inst = null;

	static function getInstance() {
		if(self::$inst == null)
			self::$inst = new database();
		return self::$inst;
	}

	private function __construct() {
		mb_internal_encoding('UTF-8');
		mb_regex_encoding('UTF-8');
		mysqli_report(MYSQLI_REPORT_STRICT);
		$dsn = 'mysql:host=' . $this->host . '; dbname=' . $this->name.'; charset=utf8';
		$options = array(
			PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
		);
		try{
			$this->db = new PDO($dsn, $this->user, $this->pass, $options);
		} catch(PDOException $e){
			exit('<p class="red"><strong>CONSTRUCT ERROR</strong></p>'.$e->getMessage());
		}
	}

	public function __destruct() {
		if(!$this->db)
			return null;
		$this->db = null;
		return null;
	}

	public function query($query) {
		$this->last_query = $query;
		try {
			$this->stmt = $this->db->prepare($query);
		} catch(PDOException $e) {
			exit('<p class="red"><strong>QUERY ERROR</strong></p>'.$e->getMessage());
		}
	}
	public function bind($param, $value, $type = null) {
		if(is_null($type))
			switch (true) {
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;
				default:
					$type = PDO::PARAM_STR;
					break;
			}
		try {
			$this->stmt->bindValue($param, $value, $type);
		} catch(PDOException $e) {
			exit('<p class="red"><strong>BIND ERROR</strong></p>'.$e->getMessage());
		}
	}
	public function execute(array $binds = null) {
		if(!isset($this->stmt))
			return false;
		try {
			if(count($binds) > 0)
				return $this->stmt->execute($binds);
			else
				return $this->stmt->execute();
		} catch(PDOException $e) {
			echo '<p class="red"><strong>EXECUTION ERROR</strong></p>'.$e->getMessage().'<p><pre>';
			var_dump($this->stmt->debugDumpParams());
			echo '</pre></p>';
			exit;
		}
	}
	public function fetch_row($shift = false) {
		if(!isset($this->stmt))
			return null;
		try {
			$this->execute();
			$ret = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
			if($shift)
				$ret = array_shift($ret);
			return $ret;
		} catch(PDOException $e) {
			exit('<p class="red"><strong>FETCH ROW ERROR</strong></p>'.$e->getMessage());
		}
	}
	public function fetch_single() {
		if(!isset($this->stmt))
			return null;
		try {
			$this->execute();
			return $this->stmt->fetchColumn(0);
		} catch(PDOException $e) {
			exit('<p class="red"><strong>FETCH SINGLE ERROR</strong></p>'.$e->getMessage());
		}
	}
	public function fetch_object() {
		if(!isset($this->stmt))
			return null;
		try {
			$this->execute();
			return $this->stmt->fetch(PDO::FETCH_OBJ);
		} catch(PDOException $e) {
			exit('<p class="red"><strong>FETCH OBJECT ERROR</strong></p>'.$e->getMessage());
		}
	}
	public function affected_rows() {
		try {
			return $this->stmt->rowCount();
		} catch(PDOException $e) {
			exit('<p class="red"><strong>AFFECTED ROWS ERROR</strong></p>'.$e->getMessage());
		}
	}
	public function num_rows() {
		try {
			return $this->stmt->fetchColumn();
		} catch(PDOException $e) {
			exit('<p class="red"><strong>NUM ROWS ERROR</strong></p>'.$e->getMessage());
		}
	}
	public function insert_id() {
		try {
			return $this->db->lastInsertId();
		} catch(PDOException $e) {
			exit('<p class="red"><strong>LAST INSERT ID ERROR</strong></p>'.$e->getMessage());
		}
	}
	public function query_error() {
		if(!isset($_SESSION['userid']))
			$_SESSION['userid'] = 0;
		if($_SESSION['userid'] == 1)
			exit('<strong>QUERY ERROR:</strong> '.$this->error.'<br />Query was '.$this->last_query);
		else
			exit('An error has been detected');
	}
	public function escape($str) {
		return $str;
	}
	public function tableExists($table) {
		try {
			$result = $this->db->query('SELECT 1 FROM `'.$table.'` LIMIT 1');
		} catch (Exception $e) {
			return false;
		}
		return $result !== false;
	}
	public function startTrans() {
		return $this->db->beginTransaction();
	}
	public function endTrans() {
		return $this->db->commit();
	}
	public function cancelTransaction() {
		return $this->db->rollBack();
	}
	public function error() {
		echo '<pre>';
		var_dump($this->stmt->debugDumpParams());
		echo '</pre>';
	}

	// Helper function(s)
	public function truncate(array $tables = null) {
		if(!count($tables))
			return false;
		$this->startTrans();
		foreach($tables as $table) {
			$this->query('TRUNCATE TABLE ?');
			$this->execute([$table]);
		}
		$this->endTrans();
	}
}
$db = database::getInstance();
$_SERVER['PHP_SELF'] = str_replace('/mtg-engine', '', $_SERVER['PHP_SELF']); // Temporary, for dev purposes