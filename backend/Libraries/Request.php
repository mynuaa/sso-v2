<?

/**
 * 封装 AJAX 传入数据的类
 * Author: Rex
 */

define('DEFAULT_PAGE_SIZE', 20);

class Request {
	private static $body = null;
	private static $pager = null;
	public static function initialize() {
		if ($_SERVER['REQUEST_METHOD'] != 'GET') {
			self::$body = [];
			$param = file_get_contents('php://input');
			$param = json_decode($param, true);
			if (is_array($param)) {
				foreach ($param as $key => $value) {
					if (is_bool($value)) {
						self::$body[$key] = intval($value);
					} else {
						self::$body[$key] = $value;
					}
				}
			}
		}
		if (isset($_GET['_pn'])) {
			self::$pager = [
				'pageNum' => intval($_GET['_pn']),
				'pageSize' => DEFAULT_PAGE_SIZE
			];
			if (isset($_GET['_ps'])) {
				self::$pager['pageSize'] = intval($_GET['_ps']);
			}
		} else {
			self::$pager = [
				'pageNum' => null,
				'pageSize' => null
			];
		}
	}
	public static function has($key) {
		return isset(self::$body[$key]);
	}
	public static function get($key) {
		if (!isset(self::$body[$key])) {
			die('Missing key in request: ' . $key);
		}
		return self::$body[$key];
	}
	public static function pageNum() {
		return self::$pager['pageNum'];
	}
	public static function pageSize() {
		return self::$pager['pageSize'];
	}
}

Request::initialize();
