<?php

namespace Apps\PHPfox_Cache_Redis\Model;

class Cache extends \Core\Extend\Cache {
	private $_client;

	public function __construct() {
		$file = PHPFOX_DIR_SETTINGS . md5('PHPfox_Cache_Redis-settings') . '.php';
		if (!file_exists($file)) {
			throw error('Redis cache not installed.');
		}

		$settings = require($file);
		if (empty($settings['redis_cache_path'])) {
			throw error('Redis is not defined by the Admin.');
		}
		$this->_client = new \Predis\Client($settings['redis_cache_path']);
	}

	public function get($name) {
		if (!$this->_client->exists($name)) {
			return false;
		}

		return json_decode($this->_client->get($name), true);
	}

	public function save($name, $value) {
		$this->_client->set($name, json_encode($value));
	}

	public function remove($name = null) {
		if ($name === null) {
			$this->_client->flushall();
			return;
		}
		$this->_client->del($name);
	}
}