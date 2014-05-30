<?php
namespace PW_Test\RecursiveElement;

require_once '../../../library/PW/RecursiveElement/RecursiveElement.php';

use PW\RecursiveElement\RecursiveElement;

class TestRecursiveElement extends \PHPUnit_Framework_TestCase 
{
	public function testCreateArray() {
    	$datas = '
    	[
    		{
    			"id": 1,
    			"id_parent":"",
    			"titre":"test 1"
    		},
    		{
    			"id": 2,
    			"id_parent":"3",
    			"titre":"test 2"
    		},
    		{
    			"id": 3,
    			"id_parent":"",
    			"titre":"test 3"
    		},
    		{
    			"id": 4,
    			"id_parent":"1",
    			"titre":"test 4"
    		},
    		{
    			"id": 5,
    			"id_parent":"2",
    			"titre":"test 5"
    		},
    		{
    			"id": 6,
    			"id_parent":"2",
    			"titre":"test 6"
    		},
    		{
    			"id": 7,
    			"id_parent":"3",
    			"titre":"test 7"
    		},
    		{
    			"id": 8,
    			"id_parent":"2",
    			"titre":"test 8"
    		},
    		{
    			"id": 9,
    			"id_parent":"6",
    			"titre":"test 9"
    		},
    		{
    			"id": 10,
    			"id_parent":"1",
    			"titre":"test 10"
    		}
    	]
    	';
    	$array =  json_decode($datas);
    	return $array;
    }
    /**
    * @depends testCreateArray
    */
    public function testGetElementInArray($array)
    {
        RecursiveElement::setElements($array, 'id', 'id_parent');
        
		$arrayRez = array();
		RecursiveElement::getElementInArray($arrayRez, 'titre');
		
		$arrayRezFinal = array(
			1 => 'test 1',
			4 => '------ test 4',
			10 => '------ test 10',
			3 => 'test 3',
			2 => '------ test 2',
			5 => '------------ test 5',
			6 => '------------ test 6',
			9 => '------------------------ test 9',
			8 => '------------ test 8',
			7 => '------ test 7'
		);

        $this->assertEquals($arrayRezFinal, $arrayRez);
    } /**
    * @depends testCreateArray
    */
    public function testGetElementWithNiveauInArray($array)
    {
        RecursiveElement::setElements($array, 'id', 'id_parent');
        
		$arrayRez = array();
		RecursiveElement::getElementWithNiveauInArray($arrayRez);
		
		$arrayRez = json_encode($arrayRez);//j'encode le résultat pour pouvoir comparer le tableau avec l'objet php (le tableau ce transforme en objet car la structure n'est pas la meme)
		
		$arrayRezFinal = '{"1":{"element":{"id":1,"id_parent":"","titre":"test 1"},"niveau":0},"4":{"element":{"id":4,"id_parent":"1","titre":"test 4"},"niveau":1},"10":{"element":{"id":10,"id_parent":"1","titre":"test 10"},"niveau":1},"3":{"element":{"id":3,"id_parent":"","titre":"test 3"},"niveau":0},"2":{"element":{"id":2,"id_parent":"3","titre":"test 2"},"niveau":1},"5":{"element":{"id":5,"id_parent":"2","titre":"test 5"},"niveau":2},"6":{"element":{"id":6,"id_parent":"2","titre":"test 6"},"niveau":2},"9":{"element":{"id":9,"id_parent":"6","titre":"test 9"},"niveau":3},"8":{"element":{"id":8,"id_parent":"2","titre":"test 8"},"niveau":2},"7":{"element":{"id":7,"id_parent":"3","titre":"test 7"},"niveau":1}}';

        $this->assertEquals($arrayRezFinal, $arrayRez);
    }
}