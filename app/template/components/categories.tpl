@categories = [data-v-component-categories] [data-v-cats]
@category = [data-v-component-categories] [data-v-cats] [data-v-cat]

@categories|deleteAllButFirstChild
@category|deleteAllButFirstChild

@categories|before = <?php
if (isset($_categories_idx)) $_categories_idx++; else $_categories_idx = 0;

$_categories = [];
if (isset($this->categories) && isset($this->categories[$_categories_idx])) {
	$_pagination_count = $this->categories[$_categories_idx]['count'];
	//$_pagination_limit = $this->categories[$_categories_idx]['limit'];
	$_categories = $this->categories[$_categories_idx]['categories'] ?? [];
	
	
}

if ($_categories) {
$generate_menu = function ($parent) use (&$_categories, &$generate_menu) {
?>
	@category|before = <?php 

	foreach($_categories as $id => $category) {
		if ($category['parent_id'] == $parent)  { ?>

		//catch all data attributes
		@category [data-v-cat-*]|innerText = $category['@@__data-v-cat-([a-zA-Z_]+)__@@']
		
		@category [data-v-cat-url]|href = <?php echo htmlentities(Vvveb\url('content/category/index', $category));?>
		@category [data-v-cat-img]|src = $category['images'][0]
		
		
		
		@category|append = <?php 
		 $generate_menu($category['taxonomy_item_id'], $_categories);
		} 
	}
	?>

	@categories|after = <?php 
}; 
reset($_categories);
$generate_menu($_categories[key($_categories)]['parent_id'], $_categories); }
?>