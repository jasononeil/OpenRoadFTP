<?php

class Upload {
	public function __construct(){}
	static function main() {
		$api = new Api();
		$api->checkLoggedIn();
		try {
			$params = php_Web::getParams();
			$folder = null;
			if($params->exists("path")) {
				$folder = $params->get("path");
			} else {
				$folder = "/";
			}
			$localPath = $_FILES["userfile"]["tmp_name"];
			$ftpPath = _hx_string_or_null($folder) . _hx_string_or_null(basename($_FILES["userfile"]["name"]));
			$api->upload($localPath, $ftpPath);
			php_Lib::hprint("success");
		}catch(Exception $__hx__e) {
			$_ex_ = ($__hx__e instanceof HException) ? $__hx__e->e : $__hx__e;
			$e = $_ex_;
			{
				php_Lib::hprint("error: " . Std::string($e));
			}
		}
	}
	function __toString() { return 'Upload'; }
}
