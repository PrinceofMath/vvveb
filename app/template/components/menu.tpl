@component  = [data-v-component-content-menu]
@categories = [data-v-component-menu] [data-v-cats]
@category = [data-v-component-menu] [data-v-cat]
@category-recursive = [data-v-component-menu] [data-v-cat-recursive] 

@categories|deleteAllButFirstChild
@category-recursive|deleteAllButFirstChild
@category|deleteAllButFirstChild

@categories|before = <?php
if (isset($_menu_idx)) $_menu_idx++; else $_menu_idx = 0;

$_categories = [];
$vvveb_is_page_edit = Vvveb\isEditor();

if (isset($this->menu) && isset($this->menu[$_menu_idx])) {
	$_pagination_count = $this->menu[$_menu_idx]['count'];
	//$_pagination_limit = $this->categories[$_menu_idx]['limit'];
	$_categories = $this->menu[$_menu_idx]['menus'] ?? [];
	if ($vvveb_is_page_edit) {
		$_categories = [
			['menu_item_id' => 1, 'parent_id' => 0, 'children' => 1, 'class' => 'vvveb-hidden'],
			['menu_item_id' => 2, 'parent_id' => 1, 'children' => 0, 'class' => 'vvveb-hidden'], 
			['menu_item_id' => 3, 'parent_id' => 0, 'children' => 0, 'class' => 'vvveb-hidden']] + $_categories;
	}
	$parent_id = 0;
}
?>


@category|before = <?php 

	foreach($_categories as $id => $category) {
		if ($category['parent_id'] == $parent_id) {
?>

		//catch all data attributes
		@category [data-v-cat-*]|innerText = $category['@@__data-v-cat-([a-zA-Z_]+)__@@']
		
		@category [data-v-cat-url]|href = <?php echo Vvveb\System\Sites::url($category['url']);?>
		@category [data-v-cat-img]|src = $category['images'][0]
		
		@category|append = <?php 
		  if ($category['children'] > 0) {
				$generate_menu($category['menu_item_id'], $_categories);
		 }
		?>

@category|after = <?php 
	}
}
?>


@category|addClass = <?php if (isset($category['class'])) echo $category['class'];?>

@category-recursive|before = <?php
$generate_menu = function ($parent_id) use (&$_categories, &$generate_menu) {
?>

@category-recursive|after = <?php 

}
?>