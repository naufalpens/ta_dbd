<?php
namespace backend\components;

class Helper{
	static function vdump($data){
		echo '<pre>' . var_export($data, true) . '</pre>';
	}
}

?>