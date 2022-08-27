@cats = [data-v-component-content-archives] [data-v-cats]

@cats|deleteAllButFirstChild
@cats [data-v-cat]|deleteAllButFirstChild

@cats|prepend = <?php
if (isset($_categories_idx)) $_categories_idx++; else $_categories_idx = 0;

$_categories = [];

if (isset($this->categories[$_categories_idx])) {
	$_pagination_count = $this->categories[$_categories_idx]['count'] ?? 0;
	//$_pagination_limit = $this->categories[$_categories_idx]['limit'];
	$_categories = $this->categories[$_categories_idx]['categories'] ?? 0;
}

if ($_categories) {
$generate_menu = function ($parent) use (&$_categories, &$generate_menu) {
?>


	@cats [data-v-cat]|before = <?php 

	foreach($_categories as $id => $category) {
		if ($category['parent_id'] == $parent) { 
	?>

		//catch all data attributes
		@cats [data-v-cat] [data-v-cat-*] = $category['@@__data-v-cat-([a-zA-Z_]+)__@@']
		
		@cats [data-v-cat] [data-v-cat-url]|href = <?php echo htmlentities(Vvveb\url('product/category/index', $category));?>
		@cats [data-v-cat] [data-v-cat-img]|src = $category['images'][0]
		
		
		
		@cats [data-v-cat]|after = <?php 
		 $generate_menu($category['taxonomy_item_id'], $_categories);
		} 
	}
	?>

	@cats|append = <?php 
}; 
reset($_categories);
$generate_menu($_categories[key($_categories)]['parent_id'], $_categories); }
?>
