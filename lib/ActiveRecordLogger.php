<?php

namespace ActiveRecord;

/**
 * Class ActiveRecordLogger
 * @package ActiveRecord
 */
class ActiveRecordLogger
{
	/**
	 * @var array
	 */
	protected static $connections = array();

	/**
	 * @var array
	 */
	protected $queries = array();

	/**
	 * @var boolean
	 */
	protected $backtrace = false;

	/**
	 * @param boolean $backtrace
	 */
	public function __construct($backtrace = false) {
		$this->backtrace = $backtrace;
	}

	/**
	 * @param \stdClass $info
	 * @param float $time
	 */
	public static function addConnectionProfile(\stdClass $info, $time)
	{
		// Create DSN using the specified info but skip the password for security reasons.
		// Example: mysql://db_user:db_password@127.0.0.1:3306/db_name?charset=utf8

		$dsn = \sprintf(
			'%s://%s:%s@%s%s/%s%s',
			$info->protocol,
			$info->user,
			'[hidden]',
			$info->host,
			!empty($info->port) ? ':' . $info->port : '',
			$info->db,
			!empty($info->charset) ? '?charset=' . $info->charset : ''
		);

		self::$connections[$dsn] = $time;
	}

	/**
	 * @return array
	 */
	public function getConnections()
	{
		return self::$connections;
	}

	/**
	 * @param string $sql
	 * @param array $values
	 */
	public function log($sql, array $values = array()) {
		$this->queries[] = array(
			'sql' => $sql,
			'params' => $values,
			'time' => 0,
			'trace' => $this->backtrace ? \call_user_func(function(){
				$backtrace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS);
				return \array_splice($backtrace, 3);
			}) : null,
		);
	}

	/**
	 * @param float $time
	 *
	 * @return ActiveRecordLogger
	 */
	public function setLastQueryTime($time) {
		$this->queries[\count($this->queries) - 1]['time'] = $time;
		return $this;
	}

	/**
	 * @param boolean $enable
	 *
	 * @return ActiveRecordLogger
	 */
	public function enableBacktrace($enable = true) {
		$this->backtrace = $enable;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getQueries() {
		return $this->queries;
	}

}
