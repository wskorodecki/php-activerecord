<?php

namespace ActiveRecord;

class ActiveRecordLogger {

	/**
	 * @var array
	 */
	protected $queries = [];

	/**
	 * @var boolean
	 */
	protected $backtrace = false;

	/**
	 * @param string $sql
	 * @param array $values
	 */
	public function log($sql, array $values) {
		$this->queries[] = [
			'sql' => $sql,
			'params' => $values,
			'time' => 0,
			'trace' => $this->backtrace ? \call_user_func(function(){
				$backtrace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS);
				return \array_splice($backtrace, 2);
			}) : null
		];
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
