<?php

class Upload {
	public function __construct(){}
	static function main() {
		$api = new Api();
		$api->checkLoggedIn();
		try {
			$params = php_Web::getParams();
			$folder = (($params->exists("path")) ? $params->get("path") : "/");
			$localPath = $_FILES["userfile"]["tmp_name"];
			$ftpPath = $folder . basename($_FILES["userfile"]["name"]);
			$api->upload($localPath, $ftpPath);
			php_Lib::hprint("success");
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				php_Lib::hprint("error: " . $e);
			}
		}
	}
	function __toString() { return 'Upload'; }
}
