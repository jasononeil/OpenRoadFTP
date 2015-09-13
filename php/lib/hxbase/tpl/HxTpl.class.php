<?php

class hxbase_tpl_HxTpl {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->assignedVariables = new haxe_ds_StringMap();
		$this->blocks = new haxe_ds_StringMap();
		$this->switches = new haxe_ds_StringMap();
		$this->loopCount = new haxe_ds_StringMap();
		$this->includeURLs = new haxe_ds_StringMap();
		$this->assignObject("this", _hx_anonymous(array("URL" => php_Web::getURI())), null);
		$this->ready = false;
	}}
	public $templateString;
	public $templateXML;
	public $ready;
	public $output;
	public $assignedVariables;
	public $switches;
	public $loopCount;
	public $includeURLs;
	public $blocks;
	public function loadTemplateFromString($tpl) {
		$this->templateString = $tpl;
		$this->templateXML = new hxbase_xml_XmlNode($this->templateString, null, null);
		$this->ready = true;
		return $this->ready;
	}
	public function loadTemplateFromFile($url) {
		$this->ready = false;
		$this->templateString = sys_io_File::getContent($url);
		$this->loadTemplateFromString($this->templateString);
		$this->ready = true;
		return $this->ready;
	}
	public function getOutput() {
		$this->output = $this->processXML($this->templateXML);
		return $this->templateXML->get_outerXML();
	}
	public function assign($name, $value, $useHTMLEncode = null) {
		if($useHTMLEncode === null) {
			$useHTMLEncode = true;
		}
		if($useHTMLEncode) {
			$value = StringTools::htmlEscape($value, null);
		} else {
			$value = $value;
		}
		$this->assignedVariables->set($name, $value);
		return $this;
	}
	public function assignObject($name, $obj, $useHTMLEncode = null) {
		if($useHTMLEncode === null) {
			$useHTMLEncode = true;
		}
		{
			$_g = 0;
			$_g1 = Reflect::fields($obj);
			while($_g < $_g1->length) {
				$propName = $_g1[$_g];
				++$_g;
				$propValue = Reflect::field($obj, $propName);
				if(Reflect::isObject($propValue) && !Std::is($propValue, _hx_qtype("String"))) {
					$this->assignObject(_hx_string_or_null($name) . "." . _hx_string_or_null($propName), $propValue, $useHTMLEncode);
				} else {
					if(!Std::is($propValue, _hx_qtype("String"))) {
						$propValue = Std::string($propValue);
					}
					$this->assign(_hx_string_or_null($name) . "." . _hx_string_or_null($propName), $propValue, $useHTMLEncode);
				}
				unset($propValue,$propName);
			}
		}
		return $this;
	}
	public function setSwitch($name, $value = null) {
		if($value === null) {
			$value = true;
		}
		$this->switches->set($name, $value);
		$switchBlock = null;
		$switchBlock = $this->getBlock($name);
		return $switchBlock;
	}
	public function newLoop($name) {
		$i = null;
		$i = 0;
		if($this->loopCount->exists($name)) {
			$i = $this->loopCount->get($name);
		}
		$i++;
		$this->loopCount->set($name, $i);
		$loopBlock = null;
		$loopBlock = $this->getBlock(_hx_string_or_null($name) . ":" . _hx_string_rec($i, ""));
		return $loopBlock;
	}
	public function useListInLoop($list, $loopName, $varName, $useHTMLEncode = null) {
		if($useHTMLEncode === null) {
			$useHTMLEncode = true;
		}
		$loopTpl = null;
		if(null == $list) throw new HException('null iterable');
		$__hx__it = $list->iterator();
		while($__hx__it->hasNext()) {
			$obj = $__hx__it->next();
			$loopTpl = $this->newLoop($loopName);
			$loopTpl->assignObject($varName, $obj, $useHTMLEncode);
		}
	}
	public function hinclude($name, $url = null) {
		if($url === null) {
			$url = "";
		}
		$includeBlock = null;
		$includeBlock = $this->getBlock($name);
		$this->includeURLs->set($name, $url);
		return $includeBlock;
	}
	public function processXML($xmlElement) {
		$list = null;
		$string = "";
		if(null == $xmlElement) throw new HException('null iterable');
		$__hx__it = $xmlElement->iterator();
		while($__hx__it->hasNext()) {
			$childElm = $__hx__it->next();
			if($childElm->get_isElement() || $childElm->get_isDocument()) {
				$string = $this->processXML_element($childElm);
			} else {
				$childElm->set_value($this->processXML_textnode($childElm->get_value()));
			}
		}
		return $string;
	}
	public function processXML_element($elm) {
		$string = "";
		if($elm->get_isDocument() || $elm->get_name() !== "hxVar" && $elm->get_name() !== "hxSwitch" && $elm->get_name() !== "hxLoop" && $elm->get_name() !== "hxInclude") {
			$string = $this->processXML_element_regularElement($elm);
		} else {
			if($elm->get_name() === "hxVar") {
				$string = $this->processXML_element_hxVar($elm);
			} else {
				$this->processXML_element_templateBlock($elm);
			}
		}
		return $string;
	}
	public function processXML_element_regularElement($elm) {
		$string = null;
		$string = "";
		if(null == $elm->getAttList()) throw new HException('null iterable');
		$__hx__it = $elm->getAttList()->iterator();
		while($__hx__it->hasNext()) {
			$attName = $__hx__it->next();
			$oldValue = null;
			$oldValue = $elm->getAtt($attName);
			if(_hx_index_of($oldValue, "{", null) !== -1) {
				$elm->setAtt($attName, $this->processXML_textnode($oldValue));
			}
			unset($oldValue);
		}
		$string .= _hx_string_or_null($this->processXML($elm));
		return $string;
	}
	public function processXML_element_hxVar($elm) {
		$varName = null;
		$varValue = null;
		$replacementElements = null;
		$varName = $elm->getAtt("name");
		if($varName !== null && $this->assignedVariables->exists($varName)) {
			$varValue = $this->assignedVariables->get($varName);
			$elm->set_outerXML($varValue);
		} else {
			$varValue = $elm->getChildren(null, null)->toString();
			$elm->set_outerXML($varValue);
		}
		return $varValue;
	}
	public function processXML_element_templateBlock($elm) {
		$string = null;
		$string = "";
		$newXML = null;
		$blockName = null;
		$newXML = "";
		$blockName = $elm->getAtt("name");
		{
			$_g = $elm->get_name();
			switch($_g) {
			case "hxSwitch":{
				$newXML = $this->processXML_element_templateBlock_hxSwitch($elm, $blockName);
			}break;
			case "hxLoop":{
				$newXML = $this->processXML_element_templateBlock_hxLoop($elm, $blockName);
			}break;
			case "hxInclude":{
				$newXML = $this->processXML_element_templateBlock_hxInclude($elm, $blockName);
			}break;
			}
		}
		$elm->set_outerXML($newXML);
		return $string;
	}
	public function processXML_element_templateBlock_hxSwitch($elm_in, $blockName) {
		$newXML = null;
		$blockTpl = null;
		$newXML = "";
		if($this->switches->exists($blockName) && $this->switches->get($blockName) === true) {
			$blockTpl = $this->getBlock($blockName);
			$blockTpl->loadTemplateFromString($elm_in->get_innerXML());
			$newXML = $blockTpl->getOutput();
		}
		return $newXML;
	}
	public function processXML_element_templateBlock_hxLoop($elm_in, $blockName) {
		$newXML = null;
		$blockTpl = null;
		$newXML = "";
		if($this->loopCount->exists($blockName)) {
			$count = null;
			$count = $this->loopCount->get($blockName);
			if($count > 0) {
				$blockTemplateXML = null;
				$blockTemplateXML = $elm_in->get_innerXML();
				{
					$_g1 = 1;
					$_g = $count + 1;
					while($_g1 < $_g) {
						$i = $_g1++;
						$blockTpl = $this->getBlock(_hx_string_or_null($blockName) . ":" . _hx_string_rec($i, ""));
						$blockTpl->loadTemplateFromString($blockTemplateXML);
						$newXML .= _hx_string_or_null($blockTpl->getOutput());
						unset($i);
					}
				}
			}
		}
		return $newXML;
	}
	public function processXML_element_templateBlock_hxInclude($elm_in, $blockName) {
		$newXML = null;
		$blockTpl = null;
		$newXML = "";
		$url = null;
		$url = "";
		if($this->includeURLs->exists($blockName) && $this->includeURLs->get($blockName) !== "") {
			$url = $this->includeURLs->get($blockName);
		} else {
			if($elm_in->hasAtt("url") && $elm_in->getAtt("url") !== "") {
				$url = $elm_in->getAtt("url");
			}
		}
		if($url !== "") {
			$blockTpl = $this->getBlock($blockName);
			$blockTpl->loadTemplateFromFile($url);
			$newXML .= _hx_string_or_null($blockTpl->getOutput());
		}
		return $newXML;
	}
	public function processXML_textnode($str_in) {
		$str_out = null;
		$str_out = "";
		$r = new EReg("{([A-Za-z0-9]+[.A-Za-z0-9]*)}", "");
		while($r->match($str_in)) {
			$varName = $r->matched(1);
			$varValue = null;
			if($this->assignedVariables->exists($varName)) {
				$varValue = $this->assignedVariables->get($varName);
			} else {
				$varValue = "";
			}
			$str_in = $r->replace($str_in, $varValue);
			unset($varValue,$varName);
		}
		$str_out = $str_in;
		return $str_out;
	}
	public function getBlock($name) {
		$newBlock = null;
		if($this->blocks->exists($name)) {
			$newBlock = $this->blocks->get($name);
		} else {
			$newBlock = new hxbase_tpl_HxTpl();
			$this->blocks->set($name, $newBlock);
		}
		return $newBlock;
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->__dynamics[$m]) && is_callable($this->__dynamics[$m]))
			return call_user_func_array($this->__dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call <'.$m.'>');
	}
	function __toString() { return 'hxbase.tpl.HxTpl'; }
}
