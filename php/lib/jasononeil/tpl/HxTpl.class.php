<?php

class jasononeil_tpl_HxTpl {
	public function __construct() {
		if( !php_Boot::$skip_constructor ) {
		$this->assignedVariables = new Hash();
		$this->blocks = new Hash();
		$this->switches = new Hash();
		$this->loopCount = new Hash();
		$this->includeURLs = new Hash();
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
		$this->templateXML = new jasononeil_xml_AdvXmlItem($this->templateString, null, null);
		$this->ready = true;
		return $this->ready;
	}
	public function loadTemplateFromFile($url) {
		$this->ready = false;
		$this->templateString = php_io_File::getContent($url);
		$this->loadTemplateFromString($this->templateString);
		$this->ready = true;
		return $this->ready;
	}
	public function getOutput() {
		$this->output = $this->processXML($this->templateXML);
		return $this->templateXML->getter_outerXml();
	}
	public function assign($name, $value, $useHTMLEncode) {
		if($useHTMLEncode === null) {
			$useHTMLEncode = true;
		}
		$value = (($useHTMLEncode) ? StringTools::htmlEscape($value) : $value);
		$this->assignedVariables->set($name, $value);
		return $this;
	}
	public function assignObject($name, $obj, $useHTMLEncode) {
		if($useHTMLEncode === null) {
			$useHTMLEncode = true;
		}
		{
			$_g = 0; $_g1 = Reflect::fields($obj);
			while($_g < $_g1->length) {
				$propName = $_g1[$_g];
				++$_g;
				$propValue = Reflect::field($obj, $propName);
				if(Reflect::isObject($propValue) && !Std::is($propValue, _hx_qtype("String"))) {
					$this->assignObject($name . "." . $propName, $propValue, $useHTMLEncode);
				}
				else {
					if(!Std::is($propValue, _hx_qtype("String"))) {
						$propValue = Std::string($propValue);
					}
					$this->assign($name . "." . $propName, $propValue, $useHTMLEncode);
				}
				unset($propValue,$propName);
			}
		}
		return $this;
	}
	public function setSwitch($name, $value) {
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
		$loopBlock = $this->getBlock($name . ":" . $i);
		return $loopBlock;
	}
	public function useListInLoop($list, $loopName, $varName, $useHTMLEncode) {
		if($useHTMLEncode === null) {
			$useHTMLEncode = true;
		}
		$loopTpl = null;
		$»it = $list->iterator();
		while($»it->hasNext()) {
		$obj = $»it->next();
		{
			$loopTpl = $this->newLoop($loopName);
			$loopTpl->assignObject($varName, $obj, $useHTMLEncode);
			;
		}
		}
	}
	public function hinclude($name, $url) {
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
		$»it = $xmlElement->iterator();
		while($»it->hasNext()) {
		$childElm = $»it->next();
		{
			if($childElm->testIsElement() || $childElm->testIsDocument()) {
				$string = $this->processXML_element($childElm);
			}
			else {
				$childElm->setter_value($this->processXML_textnode($childElm->getter_value()));
			}
			;
		}
		}
		return $string;
	}
	public function processXML_element($elm) {
		$string = "";
		if($elm->testIsDocument() || ($elm->getter_name() != "hxVar" && $elm->getter_name() != "hxSwitch" && $elm->getter_name() != "hxLoop" && $elm->getter_name() != "hxInclude")) {
			$string = $this->processXML_element_regularElement($elm);
		}
		else {
			if($elm->getter_name() == "hxVar") {
				$string = $this->processXML_element_hxVar($elm);
			}
			else {
				$this->processXML_element_templateBlock($elm);
			}
		}
		return $string;
	}
	public function processXML_element_regularElement($elm) {
		$string = null;
		$string = "";
		$»it = $elm->getAttList()->iterator();
		while($»it->hasNext()) {
		$attName = $»it->next();
		{
			$oldValue = null;
			$oldValue = $elm->getAtt($attName);
			if(_hx_index_of($oldValue, "{", null) !== -1) {
				$elm->setAtt($attName, $this->processXML_textnode($oldValue));
			}
			unset($oldValue);
		}
		}
		$string .= $this->processXML($elm);
		return $string;
	}
	public function processXML_element_hxVar($elm) {
		$varName = null;
		$varValue = null;
		$replacementElements = null;
		$varName = $elm->getAtt("name");
		if(($varName !== null) && $this->assignedVariables->exists($varName)) {
			$varValue = $this->assignedVariables->get($varName);
			$elm->setter_outerXml($varValue);
		}
		else {
			$varValue = $elm->getChildren(null, null)->toString();
			$elm->setter_outerXml($varValue);
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
		switch($elm->getter_name()) {
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
		$elm->setter_outerXml($newXML);
		return $string;
	}
	public function processXML_element_templateBlock_hxSwitch($elm_in, $blockName) {
		$newXML = null;
		$blockTpl = null;
		$newXML = "";
		if($this->switches->exists($blockName) && $this->switches->get($blockName) === true) {
			$blockTpl = $this->getBlock($blockName);
			$blockTpl->loadTemplateFromString($elm_in->getter_innerXml());
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
				$blockTemplateXML = $elm_in->getter_innerXml();
				{
					$_g1 = 1; $_g = ($count + 1);
					while($_g1 < $_g) {
						$i = $_g1++;
						$blockTpl = $this->getBlock($blockName . ":" . $i);
						$blockTpl->loadTemplateFromString($blockTemplateXML);
						$newXML .= $blockTpl->getOutput();
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
		if($this->includeURLs->exists($blockName) && $this->includeURLs->get($blockName) != "") {
			$url = $this->includeURLs->get($blockName);
		}
		else {
			if($elm_in->hasAtt("url") && $elm_in->getAtt("url") != "") {
				$url = $elm_in->getAtt("url");
			}
		}
		if($url != "") {
			$blockTpl = $this->getBlock($blockName);
			$blockTpl->loadTemplateFromFile($url);
			$newXML .= $blockTpl->getOutput();
		}
		return $newXML;
	}
	public function processXML_textnode($str_in) {
		$str_out = null;
		$str_out = "";
		$r = new EReg("{([A-Za-z0-9]+[.A-Za-z0-9]*)}", "");
		while($r->match($str_in)) {
			$varName = $r->matched(1);
			$varValue = (($this->assignedVariables->exists($varName)) ? $this->assignedVariables->get($varName) : "");
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
		}
		else {
			$newBlock = new jasononeil_tpl_HxTpl();
			$this->blocks->set($name, $newBlock);
		}
		return $newBlock;
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->»dynamics[$m]) && is_callable($this->»dynamics[$m]))
			return call_user_func_array($this->»dynamics[$m], $a);
		else
			throw new HException('Unable to call «'.$m.'»');
	}
	function __toString() { return 'jasononeil.tpl.HxTpl'; }
}
