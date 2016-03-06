<?php 
class XML{
	
	private $type;
	private $fPointer;
	private $baseUrl = "export/";	
	
	protected $nodes;
	protected $users;
	protected $DATA;
	protected $fields = array();

	function __construct($file_name){
		$this->type = array('apportaments', 'room', 'commercial', 'land', 'country', 'garage');
		$this->nodes = node_load_multiple(array(), array('type' => $this->type, 'status' => 1)); 
		$this->users = entity_load("user"); //load user informations
		$this->fPointer = fopen($this->baseUrl.$file_name, 'w') or die("Can't create/open xml file!");
	}
	
	protected function header(){}
	
	protected function footer(){}

	protected function wrapper($array, $replace_array){} 

	protected function valueReplacer($field, $value){}

	protected function addUserInformation($uid){}

	protected function dataCompilator(){}

	// Other pattern for links
	private function banSymbols($string, $link = 0){
		//format for usual text
		if(!$link) $string = preg_replace ("/[^a-zA-ZА-Яа-я0-9.,:;\s]/","",$st);
		//format for link
		// else $string = preg_replace ("/[^a-zA-ZА-Яа-я0-9.,:;\s]/","",$st);
	}

	private function recursive($array, $search = "value"){
		// recursive search value data 
		foreach ($array as $key => $value) {
			//
			if($key == $search) return $value;
			elseif(preg_match("/[field_image]/", $key)){
				$this->recursive($value, "uri");
			}	
			elseif(is_array($value)) $this->recursive($value);
		}
	}

	private function values($nodes){
		// Load node and work only object
		foreach( $nodes as $object ){
			//Work with object properties
			foreach ($object as $nid => $property){
				//Look for field value
				if(is_array($property)) $field_value[$nid][$property] = $this->recursive($property);
			}
		}
		return $field_value;
	}

	function __destruct(){
		fwrite($this->fPointer, $this->DATA);
		fclose($this->fPointer);
	}
}

class Avito extends XML{
	//fields for process

	function __construct($file_name){
		parent::__construct($file_name);
		$this->fields = array("type" => "1", "operationtype" => "1");
		$this->dataCompilator();
	}

	protected function header(){
		$this->DATA += "<Ads formatVersion=\"3\" target=\"Avito.ru\">\n";
	}
	protected function footer(){
		$this->DATA += "</Ads>\n";
	}

	protected function valueReplacer($field, $value){

	}

	protected function addUserInformation($uid){

	}

	//Get array with $field_name => value wrap in tag and add to output data
	//Wrapper can replace some keys with names from $this->fields
	protected function wrapper($array, $replace_array){
		foreach ($array as $nid){
			$this->DATA += "<Ad>\n";
			$this->DATA += "<Id>".$nid."</Id>\n";
			foreach ($nid as $key => $value) {
				//wrap in other tag
				if(array_key_exists($key, $replace_array)) $this->DATA += "<".$replace_array[$key].">".$this->banSymbols($value)."</".$replace_array[$key].">\n";	
				//wrap in the original tag if 
				elseif(array_key_exists($key, $replace_array)) $this->DATA += "<".preg_replace("field_", $key).">".$this->banSymbols($value)."</".preg_replace("field_", $key).">\n";	
				// Fields excepted from fields array are ignored			
			$this->DATA += "</Ad>\n";
			}
		}
	}

	protected function dataCompilator(){
		$this->header();
		print("DATA:\n".$this->DATA);
		$array = $this->values($this->nodes);
		echo "array:\n"; 
		print_r($array);
		$this->wrapper($array, $this->fields);
		print("wrapper: \n".$this->DATA);
		$this->footer();
		print("DATA:\n".$this->DATA);
	}
}

$Avito = new Avito("TEST_avito.xml");

?>
