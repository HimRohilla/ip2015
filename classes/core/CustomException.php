<?php
	class CustomException extends Exception {
		public function errorMessage() {
			//error message
			$errorMsg = '<b>Error: </b>on line <b>'.$this->getLine().'</b> in file '.$this->getFile()
			.' : <b>'.$this->getMessage().'</b><br>';
			return $errorMsg;
		}
		public function printStackTrace(){
			$traceArray = $this->getTrace();
			$printMsg = '<b>Error:</b> '.$this->getMessage().'<br>';
			$printMsg .= '<div style="margin-left:20px;">File: <b>'.$this->getFile().'</b> on line <b>'.$this->getLine().'</b><br>';			
			foreach ($traceArray as $key => $value) {
				$printMsg .= 'File: <b>'.$value['file'].'</b>';
				if($value['function']!=''){
					if($value['args']!=""){
						$str = "";
						for($i=0 ;$i<count($value['args']); $i++){
							$str .= $value['args'][$i];
							if($i+2 <= count($value['args']))
								$str .= ', ';
						}
					}
					$printMsg .= ' on calling function <b>'.$value['function'].'('.$str.')</b>';
				}
				$printMsg .= ' on line <b>'.$value['line'].'</b><br>';
			}
			$printMsg .='</div>';
			return $printMsg;
		}
		public function __toString(){
			return $this->errorMessage();
		}
	}
	// 		throw new CustomException($yourCustomMessage);	to throw exception
	//		echo $e->errorMessage();	to catch exception
?>