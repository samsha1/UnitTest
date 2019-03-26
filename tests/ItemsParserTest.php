<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/item_parser.php';
require_once __DIR__ . '/../app/exceptions.php';

use App\Exceptions\JsonParseException;
use App\Parser\ItemsParser;
use App\Exceptions\InvalidNumberFormatException;


class ItemsParserTest extends TestCase
{	
	var $dir = __DIR__ . '/../json/';

    public function test_create_ItemsParser() {
        $ip = new ItemsParser();
        $this->assertEquals('App\Parser\ItemsParser', \get_class($ip));
    }

	function validateParsedOutput($expected, $file) {
		$items=file_get_contents($this->dir.$file);
        $this->assertSame($expected, ItemsParser::parse($items));
    }

    public function test_parseSingleItem_shouldReturnArrayOneLabelAndValue(){
    	$this->validateParsedOutput(['1' => ['label' => 'Pizza', 'value' => '8.99']],'one.json');
    }

    private function prepareItems($file){
        return file_get_contents($this->dir.$file);
    }

    public function testParseTwoItems() {
        $this->validateParsedOutput(
            [
                '1' => ['label' => 'Pizza', 'value' => '8.99'],
                '2' => ['label' => 'Burger', 'value' => '7.88']
            ],
            'two.json'
        );
    }

    public function testCent_Conversion_toDollar(){
        $items=$this->prepareItems('one.json');
    	$itemId='1';
        $parsed= ItemsParser::parse($items);
    	$this->assertEquals(8.99,$parsed[$itemId]['value']);

    }

    public function testInvalid_JsonParse_shouldthrowException(){
        $items=$this->prepareItems('invalid.json');
        $this->expectException(JsonParseException::class);
        $parsed=ItemsParser::parse($items);
    }


    public function test_JsonParseExceptionMessage(){
        $items=$this->prepareItems('invalid.json');
        $this->expectExceptionMessage("Error parsing JSON:".$items);
        $parsed=ItemsParser::parse($items);

    }

    public function test_JsonParser_arrayKey_shouldReturnKey_label_and_value(){
        $items=$this->prepareItems('one.json');
        $itemId='1';
        $parsed=ItemsParser::parse($items);
        $this->assertArrayHasKey('label',$parsed[$itemId]);
        $this->assertArrayHasKey('value',$parsed[$itemId]);
        
    }

    public function test_emptyValue_shouldThrow_JsonParseException(){
        $items=$this->prepareItems('emptyPrice.json');
        $this->expectException(JsonParseException::class);
        $parsed=ItemsParser::parse($items);

    }

    public function test_stringPriceValueProvided_shouldThrow_InvalidNumberFormatException(){
        $items=$this->prepareItems('stringPrice.json');
        $this->expectException(InvalidNumberFormatException::class);
        $parsed=ItemsParser::parse($items);
    }
	
}

?>