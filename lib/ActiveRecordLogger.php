<?php

namespace ActiveRecord;

class ActiveRecordLogger {

	/**
	 * @var array
	 */
	private $queries = [];

	/**
	 * @param string $sql
	 * @param array $values
	 */
	public function log($sql, array $values) {
		$this->queries[] = [
			'sql' => $sql,
			'params' => $values,
			'time' => 0,
		];
	}

	public function setTime($time) {
		$this->queries[\count($this->queries) - 1]['time'] = $time;
	}

	/**
	 * @return array
	 */
	public function getQueries() {
		return $this->queries;
	}

}
