<?php

class haxe_Http {
	public function __construct($url) {
		if(!isset($this->onData)) $this->onData = array(new _hx_lambda(array(), $this, array('data'), "{
			;
		}"), 'execute1');
		if(!isset($this->onError)) $this->onError = array(new _hx_lambda(array(), $this, array('msg'), "{
			;
		}"), 'execute1');
		if(!isset($this->onStatus)) $this->onStatus = array(new _hx_lambda(array(), $this, array('status'), "{
			;
		}"), 'execute1');
		if( !php_Boot::$skip_constructor ) {
		$this->url = $url;
		$this->headers = new Hash();
		$this->params = new Hash();
		$this->cnxTimeout = 10;
		$this->noShutdown = !function_exists("stream_socket_shutdown");
	}}
	public $url;
	public $noShutdown;
	public $cnxTimeout;
	public $responseHeaders;
	public $postData;
	public $chunk_size;
	public $chunk_buf;
	public $file;
	public $headers;
	public $params;
	public function setHeader($header, $value) {
		$this->headers->set($header, $value);
	}
	public function setParameter($param, $value) {
		$this->params->set($param, $value);
	}
	public function request($post) {
		$me = $this;
		$me1 = $this;
		$output = new haxe_io_BytesOutput();
		$old = isset($this->onError) ? $this->onError: array($this, "onError");
		$err = false;
		$this->onError = array(new _hx_lambda(array("err" => &$err, "me" => &$me, "me1" => &$me1, "old" => &$old, "output" => &$output, "post" => &$post), null, array('e'), "{
			\$err = true;
			call_user_func_array(\$old, array(\$e));
		}"), 'execute1');
		$this->customRequest($post, $output, null, null);
		if(!$err) {
			$me1->onData($output->getBytes()->toString());
		}
	}
	public function fileTransfert($argname, $filename, $file, $size) {
		$this->file = _hx_anonymous(array("param" => $argname, "filename" => $filename, "io" => $file, "size" => $size));
	}
	public function customRequest($post, $api, $sock, $method) {
		$url_regexp = new EReg("^(http://)?([a-zA-Z\\.0-9-]+)(:[0-9]+)?(.*)\$", "");
		if(!$url_regexp->match($this->url)) {
			$this->onError("Invalid URL");
			return;
		}
		if($sock === null) {
			$sock = new php_net_Socket(null);
		}
		$host = $url_regexp->matched(2);
		$portString = $url_regexp->matched(3);
		$request = $url_regexp->matched(4);
		if($request == "") {
			$request = "/";
		}
		$port = ($portString === null || $portString == "" ? 80 : Std::parseInt(_hx_substr($portString, 1, strlen($portString) - 1)));
		$data = null;
		$multipart = (_hx_field($this, "file") !== null);
		$boundary = null;
		$uri = null;
		if($multipart) {
			$post = true;
			$boundary = Std::string(Std::random(1000)) . Std::string(Std::random(1000)) . Std::string(Std::random(1000)) . Std::string(Std::random(1000));
			while(strlen($boundary) < 38) $boundary = "-" . $boundary;
			$b = new StringBuf();
			$»it = $this->params->keys();
			while($»it->hasNext()) {
			$p = $»it->next();
			{
				$b->b .= "--";
				$b->b .= $boundary;
				$b->b .= "\x0D\x0A";
				$b->b .= "Content-Disposition: form-data; name=\"";
				$b->b .= $p;
				$b->b .= "\"";
				$b->b .= "\x0D\x0A";
				$b->b .= "\x0D\x0A";
				$b->b .= $this->params->get($p);
				$b->b .= "\x0D\x0A";
				;
			}
			}
			$b->b .= "--";
			$b->b .= $boundary;
			$b->b .= "\x0D\x0A";
			$b->b .= "Content-Disposition: form-data; name=\"";
			$b->b .= $this->file->param;
			$b->b .= "\"; filename=\"";
			$b->b .= $this->file->filename;
			$b->b .= "\"";
			$b->b .= "\x0D\x0A";
			$b->b .= "Content-Type: " . "application/octet-stream" . "\x0D\x0A" . "\x0D\x0A";
			$uri = $b->b;
		}
		else {
			$»it2 = $this->params->keys();
			while($»it2->hasNext()) {
			$p2 = $»it2->next();
			{
				if($uri === null) {
					$uri = "";
				}
				else {
					$uri .= "&";
				}
				$uri .= rawurlencode($p2) . "=" . rawurlencode($this->params->get($p2));
				;
			}
			}
		}
		$b2 = new StringBuf();
		if($method !== null) {
			$b2->b .= $method;
			$b2->b .= " ";
		}
		else {
			if($post) {
				$b2->b .= "POST ";
			}
			else {
				$b2->b .= "GET ";
			}
		}
		if(_hx_field(_hx_qtype("haxe.Http"), "PROXY") !== null) {
			$b2->b .= "http://";
			$b2->b .= $host;
			if($port !== 80) {
				$b2->b .= ":";
				$b2->b .= $port;
			}
		}
		$b2->b .= $request;
		if(!$post && $uri !== null) {
			if(_hx_index_of($request, "?", 0) >= 0) {
				$b2->b .= "&";
			}
			else {
				$b2->b .= "?";
			}
			$b2->b .= $uri;
		}
		$b2->b .= " HTTP/1.1\x0D\x0AHost: " . $host . "\x0D\x0A";
		if($this->postData === null && $post && $uri !== null) {
			if($multipart || $this->headers->get("Content-Type") === null) {
				$b2->b .= "Content-Type: ";
				if($multipart) {
					$b2->b .= "multipart/form-data";
					$b2->b .= "; boundary=";
					$b2->b .= $boundary;
				}
				else {
					$b2->b .= "application/x-www-form-urlencoded";
				}
				$b2->b .= "\x0D\x0A";
			}
			if($multipart) {
				$b2->b .= "Content-Length: " . (strlen($uri) + $this->file->size + strlen($boundary) + 6) . "\x0D\x0A";
			}
			else {
				$b2->b .= "Content-Length: " . strlen($uri) . "\x0D\x0A";
			}
		}
		$»it3 = $this->headers->keys();
		while($»it3->hasNext()) {
		$h = $»it3->next();
		{
			$b2->b .= $h;
			$b2->b .= ": ";
			$b2->b .= $this->headers->get($h);
			$b2->b .= "\x0D\x0A";
			;
		}
		}
		if($this->postData !== null) {
			$b2->b .= $this->postData;
		}
		else {
			$b2->b .= "\x0D\x0A";
			if($post && $uri !== null) {
				$b2->b .= $uri;
			}
		}
		try {
			if(_hx_field(_hx_qtype("haxe.Http"), "PROXY") !== null) {
				$sock->connect(new php_net_Host(haxe_Http::$PROXY->host), haxe_Http::$PROXY->port);
			}
			else {
				$sock->connect(new php_net_Host($host), $port);
			}
			$sock->write($b2->b);
			if($multipart) {
				$bufsize = 4096;
				$buf = haxe_io_Bytes::alloc($bufsize);
				while($this->file->size > 0) {
					$size = ($this->file->size > $bufsize ? $bufsize : $this->file->size);
					$len = 0;
					try {
						$len = $this->file->io->readBytes($buf, 0, $size);
					}catch(Exception $»e) {
					$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
					;
					if(($e = $_ex_) instanceof haxe_io_Eof){
						break;
					} else throw $»e; }
					$sock->output->writeFullBytes($buf, 0, $len);
					$this->file->size -= $len;
					unset($»e,$size,$len,$e,$_ex_);
				}
				$sock->write("\x0D\x0A");
				$sock->write("--");
				$sock->write($boundary);
				$sock->write("--");
			}
			$this->readHttpResponse($api, $sock);
			$sock->close();
		}catch(Exception $»e2) {
		$_ex_2 = ($»e2 instanceof HException) ? $»e2->e : $»e2;
		;
		{ $e2 = $_ex_2;
		{
			try {
				$sock->close();
			}catch(Exception $»e3) {
			$_ex_3 = ($»e3 instanceof HException) ? $»e3->e : $»e3;
			;
			{ $e1 = $_ex_3;
			{
				;
			}}}
			$this->onError(Std::string($e2));
		}}}
	}
	public function readHttpResponse($api, $sock) {
		$b = new haxe_io_BytesBuffer();
		$k = 4;
		$s = haxe_io_Bytes::alloc(4);
		$sock->setTimeout($this->cnxTimeout);
		try {
			while(true) {
				$p = $sock->input->readBytes($s, 0, $k);
				while($p !== $k) $p += $sock->input->readBytes($s, $p, $k - $p);
				{
					if($k < 0 || $k > $s->length) {
						throw new HException(haxe_io_Error::$OutsideBounds);
					}
					$b->b .= substr($s->b, 0, $k);
				}
				switch($k) {
				case 1:{
					$c = ord($s->b[0]);
					if($c === 10) {
						throw new _hx_break_exception();
					}
					if($c === 13) {
						$k = 3;
					}
					else {
						$k = 4;
					}
				}break;
				case 2:{
					$c2 = ord($s->b[1]);
					if($c2 === 10) {
						if(ord($s->b[0]) === 13) {
							throw new _hx_break_exception();
						}
						$k = 4;
					}
					else {
						if($c2 === 13) {
							$k = 3;
						}
						else {
							$k = 4;
						}
					}
				}break;
				case 3:{
					$c3 = ord($s->b[2]);
					if($c3 === 10) {
						if(ord($s->b[1]) !== 13) {
							$k = 4;
						}
						else {
							if(ord($s->b[0]) !== 10) {
								$k = 2;
							}
							else {
								throw new _hx_break_exception();
							}
						}
					}
					else {
						if($c3 === 13) {
							if(ord($s->b[1]) !== 10 || ord($s->b[0]) !== 13) {
								$k = 1;
							}
							else {
								$k = 3;
							}
						}
						else {
							$k = 4;
						}
					}
				}break;
				case 4:{
					$c4 = ord($s->b[3]);
					if($c4 === 10) {
						if(ord($s->b[2]) !== 13) {
							continue;
						}
						else {
							if(ord($s->b[1]) !== 10 || ord($s->b[0]) !== 13) {
								$k = 2;
							}
							else {
								throw new _hx_break_exception();
							}
						}
					}
					else {
						if($c4 === 13) {
							if(ord($s->b[2]) !== 10 || ord($s->b[1]) !== 13) {
								$k = 3;
							}
							else {
								$k = 1;
							}
						}
					}
				}break;
				}
				unset($p,$c4,$c3,$c2,$c);
			}
		} catch(_hx_break_exception $»e){}
		$headers = _hx_explode("\x0D\x0A", $b->getBytes()->toString());
		$response = $headers->shift();
		$rp = _hx_explode(" ", $response);
		$status = Std::parseInt($rp[1]);
		if($status === 0 || $status === null) {
			throw new HException("Response status error");
		}
		$this->onStatus($status);
		if($status < 200 || $status >= 400) {
			throw new HException("Http Error #" . $status);
		}
		$headers->pop();
		$headers->pop();
		$this->responseHeaders = new Hash();
		$size = null;
		{
			$_g = 0;
			while($_g < $headers->length) {
				$hline = $headers[$_g];
				++$_g;
				$a = _hx_explode(": ", $hline);
				$hname = $a->shift();
				$hval = ($a->length === 1 ? $a[0] : $a->join(": "));
				$this->responseHeaders->set($hname, $hval);
				if(strtolower($hname) == "content-length") {
					$size = Std::parseInt($hval);
				}
				unset($hval,$hname,$hline,$a);
			}
		}
		$chunked = $this->responseHeaders->get("Transfer-Encoding") == "chunked";
		$chunk_re = new EReg("^([0-9A-Fa-f]+)[ ]*\\r\\n", "m");
		$this->chunk_size = null;
		$this->chunk_buf = null;
		$bufsize = 1024;
		$buf = haxe_io_Bytes::alloc($bufsize);
		if($size === null) {
			if(!$this->noShutdown) {
				$sock->shutdown(false, true);
			}
			try {
				while(true) {
					$len = $sock->input->readBytes($buf, 0, $bufsize);
					if($chunked) {
						if(!$this->readChunk($chunk_re, $api, $buf, $len)) {
							break;
						}
					}
					else {
						$api->writeBytes($buf, 0, $len);
					}
					unset($len);
				}
			}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			;
			if(($e = $_ex_) instanceof haxe_io_Eof){
				;
			} else throw $»e; }
		}
		else {
			$api->prepare($size);
			try {
				while($size > 0) {
					$len2 = $sock->input->readBytes($buf, 0, ($size > $bufsize ? $bufsize : $size));
					if($chunked) {
						if(!$this->readChunk($chunk_re, $api, $buf, $len2)) {
							break;
						}
					}
					else {
						$api->writeBytes($buf, 0, $len2);
					}
					$size -= $len2;
					unset($len2);
				}
			}catch(Exception $»e2) {
			$_ex_2 = ($»e2 instanceof HException) ? $»e2->e : $»e2;
			;
			if(($e2 = $_ex_2) instanceof haxe_io_Eof){
				throw new HException("Transfert aborted");
			} else throw $»e2; }
		}
		if($chunked && ($this->chunk_size !== null || $this->chunk_buf !== null)) {
			throw new HException("Invalid chunk");
		}
		$api->close();
	}
	public function readChunk($chunk_re, $api, $buf, $len) {
		if($this->chunk_size === null) {
			if($this->chunk_buf !== null) {
				$b = new haxe_io_BytesBuffer();
				$b->b .= $this->chunk_buf->b;
				{
					if($len < 0 || $len > $buf->length) {
						throw new HException(haxe_io_Error::$OutsideBounds);
					}
					$b->b .= substr($buf->b, 0, $len);
				}
				$buf = $b->getBytes();
				$len += $this->chunk_buf->length;
				$this->chunk_buf = null;
			}
			if($chunk_re->match($buf->toString())) {
				$p = $chunk_re->matchedPos();
				if($p->len <= $len) {
					$cstr = $chunk_re->matched(1);
					$this->chunk_size = Std::parseInt("0x" . $cstr);
					if($cstr == "0") {
						$this->chunk_size = null;
						$this->chunk_buf = null;
						return false;
					}
					$len -= $p->len;
					return $this->readChunk($chunk_re, $api, $buf->sub($p->len, $len), $len);
				}
			}
			if($len > 10) {
				$this->onError("Invalid chunk");
				return false;
			}
			$this->chunk_buf = $buf->sub(0, $len);
			return true;
		}
		if($this->chunk_size > $len) {
			$this->chunk_size -= $len;
			$api->writeBytes($buf, 0, $len);
			return true;
		}
		$end = $this->chunk_size + 2;
		if($len >= $end) {
			if($this->chunk_size > 0) {
				$api->writeBytes($buf, 0, $this->chunk_size);
			}
			$len -= $end;
			$this->chunk_size = null;
			if($len === 0) {
				return true;
			}
			return $this->readChunk($chunk_re, $api, $buf->sub($end, $len), $len);
		}
		if($this->chunk_size > 0) {
			$api->writeBytes($buf, 0, $this->chunk_size);
		}
		$this->chunk_size -= $len;
		return true;
	}
	public function onData($data) { return call_user_func_array($this->onData, array($data)); }
	public $onData = null;
	public function onError($msg) { return call_user_func_array($this->onError, array($msg)); }
	public $onError = null;
	public function onStatus($status) { return call_user_func_array($this->onStatus, array($status)); }
	public $onStatus = null;
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->»dynamics[$m]) && is_callable($this->»dynamics[$m]))
			return call_user_func_array($this->»dynamics[$m], $a);
		else
			throw new HException('Unable to call «'.$m.'»');
	}
	static $PROXY = null;
	static function requestUrl($url) {
		$h = new haxe_Http($url);
		$r = null;
		$h->onData = array(new _hx_lambda(array("h" => &$h, "r" => &$r, "url" => &$url), null, array('d'), "{
			\$r = \$d;
		}"), 'execute1');
		$h->onError = array(new _hx_lambda(array("h" => &$h, "r" => &$r, "url" => &$url), null, array('e'), "{
			throw new HException(\$e);
		}"), 'execute1');
		$h->request(false);
		return $r;
	}
	function __toString() { return 'haxe.Http'; }
}
