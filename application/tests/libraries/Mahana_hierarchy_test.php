<?php
use PhpParser\Builder\Property;
use function PHPUnit\Framework\assertEquals;

class Mahana_hierarchy_test extends TestCase
{
	public function setUp(): void
	{
		$this->resetInstance();
		$this->CI->load->library('Mahana_hierarchy');
	}

	/**
	* @dataProvider provider
	*/
	public function test__recurseSort($nodeList, $parent_id, $expected) {
		$actual = $this->CI->mahana_hierarchy->_recurseSort($nodeList, $parent_id);

		assertEquals(count($expected), count($actual));
		foreach ($actual as $ndx => $item) {
			assertEquals($expected[$ndx], $item['id']);
		}
	}




	function _flatten($node, $depth, &$nodeList) {
		if (isset($node['children'])) {
			foreach ($node['children'] as $child) {
				$nodeList[] = array('id' => $child['id'], 'depth' => $depth);
				$this->_flatten($child, $depth + 1, $nodeList);
			};
		};
	}

	/**
	* @dataProvider provider
	*/
	public function test__findChildren($nodeList, $parent_id, $expected, $e_depths) {
		$actual = $this->CI->mahana_hierarchy->_findChildren($nodeList, $parent_id);
		$flattened = array();
		$this->_flatten(array('children' => $actual), 0, $flattened);
		foreach ($expected as $ndx => $id) {
			assertEquals($id, $flattened[$ndx]['id']);
			assertEquals($e_depths[$ndx], $flattened[$ndx]['depth']);
		}
	}

	public static function provider() {
		$cases = array(
			
			// Simple case - flat structure, sorted alphabetically regardless of ID or order of appearance.
			array(  
				array(
					7 => array(
						'id' => 7,
						'name' => '1st item',
						'parent_id' => 0,
						'sort_order' => 0
					),
					1 => array(
						'id' => 1,
						'name' => '3rd item',
						'parent_id' => 0,
						'sort_order' => 0
					),
					9 => array(
						'id' => 9,
						'name' => '2nd item',
						'parent_id' => 0,
						'sort_order' => 0
					),
				),
				0,
				array(7, 9, 1),
				array(0, 0, 0)
			),

			// Mixed sort - by sort_order and then alphabetically, not by ID
			array(  
				array(
					7 => array(
						'id' => 7,
						'name' => 'bbb - 3rd item',
						'parent_id' => 0,
						'sort_order' => 0
					),
					8 => array(
						'id' => 8,
						'name' => 'zzz - 1st item',
						'parent_id' => 0,
						'sort_order' => -1
					),
					 9 => array(
						'id' => 9,
						'name' => 'aaa - 2nd item',
						'parent_id' => 0,
						'sort_order' => 0
					),
				),
				0,
				array(8, 9, 7),
				array(0,0,0)
			),

			/* Add sub-items
			| Sub-items should immediately follow their parent:
			| * Items at the same 'depth', but with different parents should never be adjacent
			| * Items without a reachable parent should not be listed at all
			*/
			array(  
				array(
					8 => array(
						'id' => 8,
						'name' => 'zzz - 1st item at top level',
						'parent_id' => 0,
						'sort_order' => -1
					),
					9 => array(
						'id' => 9,
						'name' => 'aaa - 2nd item at top level',
						'parent_id' => 0,
						'sort_order' => 0
					),
					2 => array(
						'id' => 2,
						'name' => 'zzz - sub-item of the first item',
						'parent_id' => 8,
						'sort_order' => 0
					),
					1 => array(
						'id' => 1,
						'name' => 'aaa - sub-item of the second item',
						'parent_id' => 9,
						'sort_order' => 0
					),
					404 => array(
						'id' => 404,
						'name' => 'un-parented item, which should not appear',
						'parent_id' => 404,
						'sort_order' => 404
					),
				),
				0,
				array(8, 2, 9, 1),
				array(0, 1, 0, 1)
			),

			// Add a few more...
			array(  
				array(
					45 => array(
						'id' => 45,
						'name' => 'zzz - 1st item at top level',
						'parent_id' => 0,
						'sort_order' => 50
					),
					900 => array(
						'id' => 900,
						'name' => 'aaa - 2nd item at top level',
						'parent_id' => 0,
						'sort_order' => 51
					),
					77 => array(
						'id' => 77,
						'name' => 'bbb - 3rd item at top level',
						'parent_id' => 0,
						'sort_order' => 51
					),
					2 => array(
						'id' => 2,
						'name' => 'zzz - sub-item of the first item',
						'parent_id' => 45,
						'sort_order' => 0
					),
					85 => array(
						'id' => 85,
						'name' => 'aaa - first sub-item of the second item',
						'parent_id' => 900,
						'sort_order' => 0
					),
					64 => array(
						'id' => 64,
						'name' => 'ccc - sub-item of the first item',
						'parent_id' => 45,
						'sort_order' => 0
					),
					5 => array(
						'id' => 5,
						'name' => 'aaa - last sub-item of the first item',
						'parent_id' => 45,
						'sort_order' => 1
					),
					13 => array(
						'id' => 13,
						'name' => 'sub-sub item',
						'parent_id' => 5,
						'sort_order' => 1
					),
					72 => array(
						'id' => 72,
						'name' => 'sub-sub item',
						'parent_id' => 5,
						'sort_order' => 0
					),
					33 => array(
						'id' => 33,
						'name' => 'second sub-item of the second top-level item',
						'parent_id' => 900,
						'sort_order' => 0
					),
				),
				0,
				array(45, 64, 2, 5, 72, 13, 900, 85, 33, 77),
				array( 0,  1, 1, 1,  2,  2,   0,  1,  1,  0)
			),
		);

		// Test sorting the same tree, but one level down - #45 as the parent.
		$cases[] = array(
			array(
				45 => array(
					'id' => 45,
					'name' => 'final test - 1st item at top level',
					'parent_id' => 0,
					'sort_order' => 50
				),
				900 => array(
					'id' => 900,
					'name' => 'aaa - 2nd item at top level',
					'parent_id' => 0,
					'sort_order' => 51
				),
				77 => array(
					'id' => 77,
					'name' => 'bbb - 3rd item at top level',
					'parent_id' => 0,
					'sort_order' => 51
				),
				2 => array(
					'id' => 2,
					'name' => 'zzz - sub-item of the first item',
					'parent_id' => 45,
					'sort_order' => 0
				),
				85 => array(
					'id' => 85,
					'name' => 'aaa - first sub-item of the second item',
					'parent_id' => 900,
					'sort_order' => 0
				),
				64 => array(
					'id' => 64,
					'name' => 'ccc - sub-item of the first item',
					'parent_id' => 45,
					'sort_order' => 0
				),
				5 => array(
					'id' => 5,
					'name' => 'aaa - last sub-item of the first item',
					'parent_id' => 45,
					'sort_order' => 1
				),
				13 => array(
					'id' => 13,
					'name' => 'sub-sub item',
					'parent_id' => 5,
					'sort_order' => 1
				),
				72 => array(
					'id' => 72,
					'name' => 'sub-sub item',
					'parent_id' => 5,
					'sort_order' => 0
				),
				33 => array(
					'id' => 33,
					'name' => 'second sub-item of the second top-level item',
					'parent_id' => 900,
					'sort_order' => 0
				),
			),
			45,
			array(64, 2, 5, 72, 13),
			array( 0, 0, 0,  1,  1)
		);

		return $cases;
	}
}

?>
