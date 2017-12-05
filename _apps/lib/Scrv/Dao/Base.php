<?php
/**
 * lib/Scrv/Dao/Base.php
 * @author mgng
 */

namespace lib\Scrv\Dao;
use lib\Scrv as Scrv;

/**
 * DAO 基底クラス(PDO使用)
 *
 * usage:
 *
 * <pre>
 * $dao = new \lib\Scrv\Dao\Base();
 * $dao->connect("/path/to/db/config.ini");
 * $result = $dao->query("select * from table order by column");
 * $result = $dao->select(
 *   "select * from table where id=:id and age>:age",
 *   ["id" => $id, "age" => (int)$age,]
 * );
 * </pre>
 *
 * 参考: http://qiita.com/mpyw/items/b00b72c5c95aac573b71
 *
 * @author mgng
 */
class Base extends Scrv\Base
{
	/**
	 * PDO オブジェクト
	 * @var Object
	 */
	protected static $_pdo = null;

	/**
	 * エラーメッセージ格納用
	 * @var string
	 */
	protected $_error_message = "";

	/**
	 * 接続設定格納配列
	 * @var type
	 */
	protected $_con_settings = array();

	/**
	 * クエリキャッシュ用ディレクトリ
	 * @var string
	 */
	protected $_cache_dir = "";

	/**
	 * コンストラクタ
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_cache_dir = __DIR__ . "/../../../" . self::$_common_ini["db"]["cache_dir"];
	}

	/**
	 * デストラクタ
	 */
	public function __destruct()
	{
	}

	/**
	 * DBに接続する。接続成功時はtrue、失敗時はfalseを返す
	 * @param array $ini
	 * @return boolean
	 */
	public function connect(array $ini)
	{
		if ( isset(self::$_pdo) ) {
			return true;
		}
		if ( ! isset( $ini["driver"],$ini["host"],$ini["port"],$ini["dbname"],$ini["charset"],$ini["username"],$ini["password"] ) ){
			$this->_error_message = "setting error";
			return false;
		}
		try{
			$dsn = "{$ini["driver"]}:host={$ini["host"]};port={$ini["port"]};dbname={$ini["dbname"]};charset={$ini["charset"]}";
			$username = $ini["username"];
			$password = $ini["password"];
			$options = $this->_getOptions($ini);
			// $this->_pdo = new \PDO( $dsn, $username, $password, $options);
			self::$_pdo = new \PDO( $dsn, $username, $password, $options);
			if ( isset( $ini["timezone"] ) ) {
				self::$_pdo->query("SET SESSION time_zone = '{$ini["timezone"]}'");
			}
			if ( isset( $ini["sql_mode"] ) ) {
				self::$_pdo->query("SET SESSION sql_mode = '{$ini["sql_mode"]}'");
			}
			return true;
		} catch( \PDOException $e ) {
			$this->_error_message = $e->getMessage();
			return false;
		}
	}

	/**
	 * DB切断する
	 * @return boolean
	 */
	public function disconnect()
	{
		//$this->_pdo = null;
		self::$_pdo = null;
		return true;
	}

	/**
	 * PDOに渡すオプション配列を返す
	 * @param array $ini 設定連想配列
	 * @return array
	 */
	private function _getOptions(array $ini)
	{
		// 基本オプション
		$options = array(
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
			\PDO::ATTR_EMULATE_PREPARES => false,
		);
		// 持続的接続
		if ( isset($ini["persistent"]) ) {
			$options[\PDO::ATTR_PERSISTENT] = $ini["persistent"] === "1" ? true : false;
		}
		// タイムアウト設定
		if ( isset($ini["timeout"]) && ctype_digit($ini["timeout"]) ) {
			$options[\PDO::ATTR_TIMEOUT] = (int)$ini["timeout"];
		}
		// mysql固有の設定
		if ( $ini["driver"] === 'mysql' ) {
			// バッファクエリ使用
			if ( $ini["bufferd"] === "1" ) {
				$options[\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY] = true;
			}
		}
		return $options;
	}

	/**
	 * エラーメッセージを返す
	 * @return string
	 */
	public function getErrorMessage()
	{
		return $this->_error_message;
	}

	/**
	 * 生成したPDOオブジェクトを返す
	 * @return PDO Object
	 */
	public function getPdoObject()
	{
		//return $this->_pdo;
		return self::$_pdo;
	}

	/**
	 * 素のSQLを実行し結果セットを返す。
	 * @param string $sql
	 * @return array
	 */
	public function query($sql)
	{
		//$stmt = $this->_pdo->query($sql);
		$stmt = self::$_pdo->query($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	/**
	 * 素のSQLを実行し作用した行数を返す。
	 * delete,insert,updateおよび結果を必要としない場合のクエリに使う。
	 * @param string $sql
	 * @return integer
	 */
	public function exec($sql)
	{
		//return $this->_pdo->exec($sql);
		return self::$_pdo->exec($sql);
	}

	/**
	 * DBキャッシュファイルパスを返す
	 * @param string $sql
	 * @param array $params
	 * @param string $index
	 * @return string
	 */
	private function _getCacheFile($sql, array $params, $index = "")
	{
		return $this->_cache_dir . "{$index}_" . sha1($sql . print_r($params, 1));
	}

	/**
	 * キャッシュファイルを作成する
	 * @param string $filepath
	 * @param string $contents
	 * @param integer $expire キャッシュ秒数
	 */
	private function _makeCacheFile($filepath, $contents, $expire)
	{
		file_put_contents($filepath, $contents, LOCK_EX);
		touch($filepath, self::$_nowTimestamp + $expire);
		// XXX キャッシュが溜まってしまうのでゴミを削除
		// 暫定で作成から1時間経過していたら削除
		$gc_sec = (int)self::$_common_ini["db"]["cache_gc_sec"];
		foreach(glob($this->_cache_dir . "*") as $cache_file){
			if (filemtime($cache_file) + $gc_sec < self::$_nowTimestamp ) {
				unlink($cache_file);
			}
		}
	}

	/**
	 * sqlと埋め込むパラメータを渡してSQLを実行、結果セットを返す。
	 *
	 * $cache_setting の形式は以下の通り。
	 * キャッシュファイルは、index + sha1(sql+パラメータ配列) で作成される。
	 * <pre>
	 * [
	 *	"enabled" => boolean
	 *	"expire" => キャッシュさせる秒数
	 *	"index" => キャッシュファイル名接頭辞。使わない場合は空文字を設定
	 * ]
	 * </pre>
	 *
	 * @param string $sql SQL文 パラメータは名前付きプレースホルダで指定する
	 * @param array $params 連想配列(key=>value)で埋め込むパラメータを指定。valueは明示的に型指定(string,int等)を行うこと。
	 * @param array $cache_setting 実行結果をキャッシュするかの設定
	 * @return array
	 */
	public function select($sql, array $params=array(), array $cache_setting=array())
	{
		// キャッシュ有効な場合はファイル検索
		// 存在するかつ有効期限内であれば返す
		if ( isset($cache_setting["enabled"]) && $cache_setting["enabled"] ) {
			$cache_file_path = $this->_getCacheFile($sql, $params, $cache_setting["index"]);
			if ( is_readable($cache_file_path) && filemtime($cache_file_path) + $cache_setting["expire"] > self::$_nowTimestamp ) {
				return unserialize(file_get_contents($cache_file_path));
			}
		}

		$stmt = self::$_pdo->prepare($sql);
		foreach( $params as $key => $value ) {
			$stmt->bindValue(":{$key}", $value, $this->_getDataType($value));
		}
		$stmt->execute();
		$result = $stmt->fetchAll();

		if ( isset($cache_setting["enabled"]) && $cache_setting["enabled"] ) {
			$seri = serialize($result);
			$cache_file_path = $this->_getCacheFile($sql, $params, $cache_setting["index"]);
			$this->_makeCacheFile($cache_file_path, $seri, $cache_setting["expire"]);
		}

		return $result;
	}

	/**
	 * selectと同じだが、PDOStatementを返す。行単位で処理したい場合に利用。
	 * @param type $sql
	 * @param array $params
	 * @return PDOStatement
	 */
	public function selectStmt($sql, array $params=array())
	{
		//$stmt = $this->_pdo->prepare($sql);
		$stmt = self::$_pdo->prepare($sql);
		foreach( $params as $key => $value ) {
			$stmt->bindValue(":{$key}", $value, $this->_getDataType($value));
		}
		$stmt->execute();
		return $stmt;
	}

	/**
	 * 削除処理を実行。削除された行数を返す。
	 * @param string $sql
	 * @param array $params
	 * @return int
	 */
	public function delete($sql, array $params=array())
	{
		//$stmt = $this->_pdo->prepare($sql);
		$stmt = self::$_pdo->prepare($sql);
		foreach( $params as $key => $value ) {
			$stmt->bindValue(":{$key}", $value, $this->_getDataType($value));
		}
		$stmt->execute();
		return $stmt->rowCount();
	}

	/**
	 * 挿入処理を実行。挿入された行数を返す。
	 * @param string $sql
	 * @param array $params
	 * @return int
	 */
	public function insert($sql, array $params=array())
	{
		//$stmt = $this->_pdo->prepare($sql);
		$stmt = self::$_pdo->prepare($sql);
		foreach( $params as $key => $value ) {
			$stmt->bindValue(":{$key}", $value, $this->_getDataType($value));
		}
		$stmt->execute();
		return $stmt->rowCount();
	}

	/**
	 * 更新処理を実行。更新された行数を返す。
	 * @param string $sql
	 * @param array $params
	 * @return int
	 */
	public function update($sql, array $params=array())
	{
		//$stmt = $this->_pdo->prepare($sql);
		$stmt = self::$_pdo->prepare($sql);
		foreach( $params as $key => $value ) {
			$stmt->bindValue(":{$key}", $value, $this->_getDataType($value));
		}
		$stmt->execute();
		return $stmt->rowCount();
	}

	/**
	 * 最後に挿入したidを返す
	 * @param string $name
	 * @return int
	 */
	public function lastInsertId($name)
	{
		return self::$_pdo->lastInsertId($name);
	}

	/**
	 * トランザクションを開始する。
	 * @return boolean
	 */
	public function beginTransaction()
	{
		//return $this->_pdo->beginTransaction();
		if ( ! self::$_pdo->inTransaction() ) {
			return self::$_pdo->beginTransaction();
		}
		return true;
	}

	/**
	 * ロールバックを実行する。
	 * @return boolean
	 */
	public function rollBack()
	{
		//return $this->_pdo->rollBack();
		return self::$_pdo->rollBack();
	}

	/**
	 * コミット処理を実行する。
	 * @return boolean
	 */
	public function commit()
	{
		//return $this->_pdo->commit();
		return self::$_pdo->commit();
	}

	/**
	 * like構文用のメタ文字エスケープを行う
	 * @param string $param
	 * @param string $escape default "!"
	 * @return type
	 */
	public function escapeForLike($param, $escape = "!")
	{
		return preg_replace('/(?=[!_%])/', $escape, $param);
	}

	/**
	 * 登録対象データの型を返す。
	 * @param mixed $var
	 * @return mixed
	 */
	private function _getDataType($var)
	{
		if (is_string($var)) {
			return \PDO::PARAM_STR;
		}
		if (is_int($var)) {
			return \PDO::PARAM_INT;
		}
		if (is_bool($var)) {
			return \PDO::PARAM_BOOL;
		}
		if (is_null($var)){
			return \PDO::PARAM_NULL;
		}
		return \PDO::PARAM_STR;
	}


}
