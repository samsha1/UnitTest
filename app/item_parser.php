<?php
namespace App\Parser;

require_once 'exceptions.php';

use App\Exceptions\JsonParseException;
use App\Exceptions\InvalidNumberFormatException;

class ItemsParser {

	public static function parse($response){
			$jDecode=json_decode($response);
			if (JSON_ERROR_NONE !== json_last_error()){
           		throw new JsonParseException('Error parsing JSON:'.$response);
           	}
			$jd=$jDecode->_embedded->menu_items;
			$parsed = [];

			foreach($jd as $key => $item){
				$price=$item->price_per_unit;
				if (is_string($price)) {
					throw new InvalidNumberFormatException('Invalid numeric value encountered:'.$price);
				}
				 $parsed[$item->id] = [
                    "label" => $item->name,
                    "value" => number_format(($price/100), 2, '.', ' ')
                ];
  			}	
  			return $parsed;

		}

}

?>