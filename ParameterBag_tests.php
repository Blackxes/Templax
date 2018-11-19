<?php
//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * provides functional handling with a property
 * also provides hooks for properties when they may need to be parsed beforehand
 * 
 * @author: Alexander Bassov
 * 
/*********************************************************************************************/

error_reporting( E_ALL & ~E_NOTICE );
ini_set( "display_error", 1 );

require_once( __DIR__ . "/v4/Source/Classes/ParameterBag.php" );

//_____________________________________________________________________________________________
// test bad
class Bag extends \Templax\Source\Classes\ParameterBag {
	public function __construct() {
		parent::__construct( array(
			"0_level1_1" => "String",
			"0_level1_2" => 0,
			"0_level1_3" => 5,
			"0_level1_4" => 0.5,
			"0_level1_5" => true,
			"0_level1_6" => false,
			"0_level1_7" => null,
			"0_level1_8" => array(
				"7_level2_1" => "7_level2_1",
				"7_level2_2" => "7_level2_2"
			),
			"0_level1_9" => new \Templax\Source\Classes\ParameterBag( array(
				"9_level2_1" => "String",
				"9_level2_2" => 0,
				"9_level2_3" => 5,
				"9_level2_4" => 0.5,
				"9_level2_5" => true,
				"9_level2_6" => false,
				"9_level2_7" => null,
				"9_level2_8" => array(
					"8_level3_1" => "8_level3_1",
					"8_level3_2" => "8_level3_2"
				),
				"9_level2_9" => new \Templax\Source\Classes\ParameterBag( array(
					"9_level3_1" => "String",
					"9_level3_2" => 0,
					"9_level3_3" => 5,
					"9_level3_4" => 0.5,
					"9_level3_5" => true,
					"9_level3_6" => false,
					"9_level3_7" => null,
					"9_level3_8" => array(
						"8_level4_1" => "8_level4_1",
						"8_level4_2" => "8_level4_2"
					),
				))
			))
		));
	}
}

//_____________________________________________________________________________________________
// tests containing class
class ParameterBag_Tests {

	private $hr = "<hr />";

	public function __construct() {
	}

	public function print( ...$messages ) {
		foreach( $messages as $i => $key )
			print_r( $message );
	}

	public function vdump( ...$message ) {
		foreach( $messages as $i => $key )
			var_dump( $message );
	}

	public function line() {
		print_r("<hr>");
	}

	public function t_get( $bag ) {

		print_r( "get 0_level1_1 expecting 'String'" );
		var_dump( $bag->get("0_level1_1") );
		echo $this->hr;

		print_r( "get 9_level2_1 expecting 'String'" );
		var_dump( $bag->get([ "0_level1_9", "9_level2_1" ]) );
		echo $this->hr;
	}

	public function t_values( $bag ) {

		print_r( "values of this level - expecting success" );
		var_dump( $bag->values() );
		echo $this->hr;

		print_r( "values of 0_level1_8 - expecting fail");
		var_dump( $bag->values("0_level1_8") );
		echo $this->hr;

		print_r( "values of 0_level1_9 - expecting success" );
		var_dump( $bag->values("0_level1_9") );
		echo $this->hr;

		print_r( "values of [0_level1_9, 9_level2_9] - expecting success" );
		var_dump( $bag->values(["0_level1_9", "9_level2_9"]) );
		echo $this->hr;

		print_r( "values of [0_level1_9, 9_level2_8] - expecting fail" );
		var_dump( $bag->values(["0_level1_9", "9_level2_8"]) );
		echo $this->hr;

		print_r( "values of [0_level1_9, 9_level2_9, 9_level3_8] - expecting fail" );
		var_dump( $bag->values(["0_level1_9", "9_level2_9", "9_level3_8"]) );
		echo $this->hr;
	}

	public function t_keys( $bag ) {

		print_r( "keys of this level - expecting success (0_level1_x)" );
		var_dump( $bag->keys() );
		echo $this->hr;

		print_r( "keys of 0_level1_8 - expecting fail");
		var_dump( $bag->keys("0_level1_8") );
		echo $this->hr;

		print_r( "keys of 0_level1_9 - expecting success (9_level2_x)" );
		var_dump( $bag->keys("0_level1_9") );
		echo $this->hr;

		print_r( "keys of [0_level1_9, 9_level2_9] - expecting success (9_level3_x)" );
		var_dump( $bag->keys(["0_level1_9", "9_level2_9"]) );
		echo $this->hr;

		print_r( "keys of [0_level1_9, 9_level2_8] - expecting fail" );
		var_dump( $bag->keys(["0_level1_9", "9_level2_8"]) );
		echo $this->hr;

		print_r( "keys of [0_level1_9, 9_level2_9, 9_level3_8] - expecting fail" );
		var_dump( $bag->keys(["0_level1_9", "9_level2_9", "9_level3_8"]) );
		echo $this->hr;
	}

	public function t_all( $bag ) {

		print_r( "all of this level - expecting success" );
		var_dump( $bag->all() );
		echo $this->hr;

		print_r( "all of 0_level1_8 - expecting fail");
		var_dump( $bag->all("0_level1_8") );
		echo $this->hr;

		print_r( "all of 0_level1_9 - expecting success" );
		var_dump( $bag->all("0_level1_9") );
		echo $this->hr;

		print_r( "all of [0_level1_9, 9_level2_9] - expecting success" );
		var_dump( $bag->all(["0_level1_9", "9_level2_9"]) );
		echo $this->hr;

		print_r( "all of [0_level1_9, 9_level2_8] - expecting fail" );
		var_dump( $bag->all(["0_level1_9", "9_level2_8"]) );
		echo $this->hr;

		print_r( "all of [0_level1_9, 9_level2_9, 9_level3_8] - expecting fail" );
		var_dump( $bag->all(["0_level1_9", "9_level2_9", "9_level3_8"]) );
		echo $this->hr;
	}

	public function t_has( $bag ) {

		print_r( "1. has - 0_level1_1 - expecting success" );
		var_dump( $bag->has("0_level1_1") );
		echo $this->hr;

		print_r( "2. has - 0_level1_7 - expecting success" );
		var_dump( $bag->has("0_level1_7") );
		echo $this->hr;

		print_r( "3. has - 0_level1 - expecting fail" );
		var_dump( $bag->has("0_level1") );
		echo $this->hr;

		print_r( "4. has - empty check" );
		var_dump( $bag->has("") );
		echo $this->hr;

		print_r( "5. has - 0_level1_9 - expecting success" );
		var_dump( $bag->has("0_level1_9") );
		echo $this->hr;

		print_r( "6. has - [0_level1_9, 9_level2_1] - expecting success" );
		var_dump( $bag->has(["0_level1_9", "9_level2_1"]) );
		echo $this->hr;

		print_r( "7. has - [0_level1_9, 9_level2_7] - expecting success" );
		var_dump( $bag->has(["0_level1_9", "9_level2_7"]) );
		echo $this->hr;

		print_r( "8. has - [0_level1_9, 9_level2_8, 8_level3_1] - expecting fail" );
		var_dump( $bag->has(["0_level1_9", "9_level2_8", "8_level3_1"]) );
		echo $this->hr;

		print_r( "9. has - [0_level1_9, 9_level2_9, 9_level3_6] - expecting success" );
		var_dump( $bag->has(["0_level1_9", "9_level2_9", "9_level3_6"]) );
		echo $this->hr;
	}

	public function t_merge( $bag ) {

		// merging into an regular array level1
		print_r( "1. merge - 0_level1_8 - expecting fail (merge 0_level2_8)" );
		print_r( $bag->merge("0_level1_8", array("0_level2_8" => "new_item")) );
		echo $this->hr;

		// merging into a bag level1
		print_r( "2. merge - this - expecting success (merge 0_level1_)" );
		print_r( $bag->merge(null, array("0_level1_" => "new item")) );
		echo $this->hr;

		// merging into a bag level2
		print_r( "3. merge - 0_level1_9 - expecting success" );
		print_r( $bag->merge("0_level1_9", array("0_level2_9" => "new item" )) );
		echo $this->hr;

		// merging into a string level3
		print_r( "4. merge - [0_level1_9, 9_level2_1] - expecting fail" );
		print_r( $bag->merge(["0_level1_9", "9_level2_1"], array("9_level3_1" => "new item")) );
		echo $this->hr;

		// merging into an array level3
		print_r( "5. merge - [0_level1_9, 9_level2_8] - expecting fail" );
		print_r( $bag->merge(["0_level1_9", "9_level2_8"], array("9_level3_8" => "new item")) );
		echo $this->hr;

		// merging into a bag level3
		print_r( "6. merge - [0_level1_9, 9_level2_9] - expecting success" );
		print_r( $bag->merge(["0_level1_9", "9_level2_9"], array("9_level3_9" => "new item")) );
		echo $this->hr;
	}

	public function t_rMerge( $bag ) {

		// merging into a bag level1
		print_r( "1. rMerge - this - expecting success (underwrite '0_level1_1' with 'new item')" );
		print_r( $bag->merge(null, array("0_level1_1" => "new item")) );
		echo $this->hr;

		// merging into an regular array level2
		print_r( "2. rMerge - 0_level1_8 - expecting fail (underwrite '7_level2_1' with 'new item')" );
		print_r( $bag->merge("0_level1_8", array("7_level2_1" => "new_item")) );
		echo $this->hr;

		// merging into a bag level2
		print_r( "3. merge - 0_level1_9 - expecting success (underwrite '9_level2_1' with 'new item')" );
		print_r( $bag->merge("0_level1_9", array("9_level2_1" => "new item" )) );
		echo $this->hr;

		// merging into a string level3
		print_r( "4. merge - [0_level1_9, 9_level2_8] - expecting fail (undewrite '9_level2_8' with 'new item')" );
		print_r( $bag->merge(["0_level1_9", "9_level2_8"], array("8_level3_1" => "new item")) );
		echo $this->hr;

		// merging into an array level3
		print_r( "5. merge - [0_level1_9, 9_level2_9] - expecting success (underwrite '9_level2_9' with 'new item')" );
		print_r( $bag->merge(["0_level1_9", "9_level2_9"], array("9_level3_1" => "new item")) );
		echo $this->hr;
	}

	public function t_replace( $bag ) {

		// replace array level3
		print_r( "1. replace - [0_level1_9, 9_level2_9, 9_level3_8] - expecting fail" );
		print_r( $bag->replace(["0_level1_9", "9_level2_9", "9_level3_8"], array("REPLACEMENT" => "new item")) );
		echo $this->hr;

		// replace bag level3
		print_r( "2. replace - [0_level1_9, 9_level2_9] - expecting success" );
		print_r( $bag->replace(["0_level1_9", "9_level2_9"], array("REPLACEMENT" => "new item")) );
		echo $this->hr;

		// replace integer level2
		print_r( "3. replace - [0_level1_9, 9_level2_3] - expecting fail" );
		print_r( $bag->replace(["0_level1_9", "9_level2_3"], array("REPLACEMENT" => "new item")) );
		echo $this->hr;

		// replace bag level1
		print_r( "4. replace - 0_level1_9 - expecting success" );
		print_r( $bag->replace("0_level1_9", array("REPLACEMENT" => "new item")) );
		echo $this->hr;

		// replace this level1
		print_r( "5. replace - this - expecting success" );
		print_r( $bag->replace(null, array("REPLACEMENT" => "new item")) );
		echo $this->hr;
	}

	public function t_remove( $bag ) {

		// remove item level3
		print_r( "1. remove - [0_level1_9, 9_level2_9, 9_level3_5] - expecting success" );
		var_dump( $bag->remove(["0_level1_9", "9_level2_9", "9_level3_5"]) );
		print_r( $bag );
		echo $this->hr;

		// remove item level3
		print_r( "2. remove - [0_level1_9, 9_level2_9, SOME_ITEM] - expecting fail" );
		var_dump( $bag->remove(["0_level1_9", "9_level2_9", "SOME_ITEM"]) );
		print_r( $bag );
		echo $this->hr;

		// remove bag level2
		print_r( "3. remove - [0_level1_9, 9_level2_9] - expecting success" );
		var_dump( $bag->remove(["0_level1_9", "9_level2_9"]) );
		print_r( $bag );
		echo $this->hr;

		// remove item level2
		print_r( "4. remove - [0_level1_9, 9_level2_3] - expecting success" );
		var_dump( $bag->remove(["0_level1_9", "9_level2_3"]) );
		print_r( $bag );
		echo $this->hr;

		// remove item level1
		print_r( "5. remove - 0_level1_7 - expecting success" );
		var_dump( $bag->remove("0_level1_7") );
		print_r( $bag );
		echo $this->hr;
	}

	public function t_isNull( $bag ) {

		// only one level is needed to be checked
		
		// check on null level3
		print_r( "is null - [0_level1_9, 9_level2_9, 9_level3_7] - value is 'null' - expecting true" );
		var_dump( $bag->isNull(["0_level1_9", "9_level2_9", "9_level3_7"]) );
		echo $this->hr;

		// check false level3
		print_r( "is null - [0_level1_9, 9_level2_9, 9_level3_6] - value is 'false' expecting false" );
		var_dump( $bag->isNull(["0_level1_9", "9_level2_9", "9_level3_6"]) );
		echo $this->hr;

		// check true level3
		print_r( "is null - [0_level1_9, 9_level2_9, 9_level3_5] - value is 'false' expecting false" );
		var_dump( $bag->isNull(["0_level1_9", "9_level2_9", "9_level3_5"]) );
		echo $this->hr;
	}

	public function t_set( $bag ) {

		// set value level1
		print_r( "set - 0_level1_3 - set from '5' to 'new value'" );
		var_dump( $bag->all() );
		$bag->set("0_level1_3", "new value");
		var_dump( $bag->all() );
		echo $this->hr;

		// set value level2
		print_r( "set - [0_level1_9, 9_level2_6] - set from 'false' to 'new value'" );
		var_dump( $bag->get(["0_level1_9"]) );
		$bag->set(["0_level1_9", "9_level2_6"], "new value");
		var_dump( $bag->get(["0_level1_9"]) );
		echo $this->hr;
	}
}

//_____________________________________________________________________________________________
//

echo "<h1>Starting Tests</h1>";

$bag = new Bag();
$test = new ParameterBag_Tests();

echo "<pre>";

// tests are standalone tests - when calling everything
// results may differ from expectation

// $test->t_get( $bag );
// $test->t_values( $bag );
// $test->t_keys( $bag );
// $test->t_all( $bag );
// $test->t_has( $bag );
// $test->t_merge( $bag );
// $test->t_rMerge( $bag );
// $test->t_replace( $bag );
// $test->t_remove( $bag );
// $test->t_isNull( $bag );
// $test->t_set( $bag );

echo "</pre>";

//_____________________________________________________________________________________________
//