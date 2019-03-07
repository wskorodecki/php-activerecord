<?php

namespace ActiveRecord;

class File {

	private $cache_folder = null;

	public function __construct($options) {
		$folder = rtrim($options['path'], '/');
		if (isset($options['host']) && DIRECTORY_SEPARATOR == '\\') {
			$folder = $options['host'] . ':/' . $folder;
		}

		if (!is_dir($folder) && !mkdir($folder, 0777, true)) {
			throw new \Exception(sprintf("Cache folder %s does not exists!", $folder));
		} else {
			$this->cache_folder = $folder;
		}
	}

	private function cache_file($key) {
		return sprintf('%s/parcf_%s.php', $this->cache_folder, md5($key));
	}
	
	public function flush() {
		foreach (glob(sprintf('%s/parcf_%s.php', $this->cache_folder, '*')) as $cache_file) {
			unlink($cache_file);
		}
	}

	public function read($key) {
		$cache_file = $this->cache_file($key);
		if (!file_exists($cache_file)) {
			return null;
		}
		$contenst = include $cache_file;
		if (!isset($contenst['expire']) || ($contenst['expire'] > 0 && $contenst['expire'] < time())) {
			return null;
		} else {
			return $contenst['value'];
		}
	}

	public function write($key, $value, $expire) {
		$data = [
			'key' => $key,
			'expire' => $expire ? time() + $expire : 0,
			'value' => $value
		];
		$contents = '<?php return ' . var_export($data, 1) . ';';
		return file_put_contents($this->cache_file($key), $contents);
	}

}
