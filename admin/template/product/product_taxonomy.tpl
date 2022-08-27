/* taxonomies */
@taxonomy_item = [data-v-taxonomies] [data-v-taxonomy_item]
@taxonomy_item|deleteAllButFirstChild

@taxonomy_item|before = <?php
if(isset($this->taxonomies) && is_array($this->taxonomies)) 
{
	//$pagination = $this->taxonomies[$_taxonomies_idx]['pagination'];
	foreach ($this->taxonomies as $index => $taxonomy_item) {?>
	
	@taxonomy_item [data-v-taxonomy_item-*]|innerText = $taxonomy_item['@@__data-v-taxonomy_item-([a-zA-Z_]+)__@@']
	@taxonomy_item [data-v-taxonomy_item-*]|title = $taxonomy_item['@@__data-v-taxonomy_item-([a-zA-Z_]+)__@@']
	
	@taxonomy_item [data-taxonomy_id]|data-taxonomy = $taxonomy_item['taxonomy']
	@taxonomy_item [data-taxonomy_id]|data-taxonomy_id = $taxonomy_item['taxonomy_id']
	
	@taxonomy_item input[data-v-post-taxonomy_item-*]|value = $taxonomy_itemItem['@@__data-v-post-taxonomy_item-([a-zA-Z_]+)__@@']
	@taxonomy_item input[data-v-post-taxonomy_item-taxonomy_item_id]|addNewAttribute = <?php if (isset($taxonomy_itemItem['checked']) && $taxonomy_itemItem['checked']) echo 'checked';?>

	@taxonomy_item [data-v-taxonomy_item-url]|title = $taxonomy_item['name']	
	@taxonomy_item a[data-v-edit-url]|href = <?php echo \Vvveb\url(['module' => 'content/taxonomy_item', 'taxonomy_id' => $taxonomy_itemItem['taxonomy_id']]);?>
	
	
	@taxonomy_item|after = <?php 
	} 
}?>


/* categories */
@categories = [data-v-categories] [data-v-cats]
@category = [data-v-categories] [data-v-cats] [data-v-cat]
@language = [data-v-languages] [data-v-language]

@categories|deleteAllButFirstChild
@category|deleteAllButFirstChild

@categories|before = <?php
$_categories = $taxonomy_item['taxonomy_item']['categories'] ?? [];
if ($_categories) {
	$generate_menu = function ($parent) use (&$_categories, &$generate_menu, $taxonomy_item) {
		
	$hasChildren = false;	
	foreach($_categories as $id => $taxonomy_itemItem) {
		if ($taxonomy_itemItem['parent_id'] == $parent) {
			$hasChildren = true;
			break;
		}
	}
	if (!$hasChildren) return;	
?>

	@category|before = <?php 

	foreach($_categories as $id => $taxonomy_itemItem) {
		if ($taxonomy_itemItem['parent_id'] == $parent) {?>

		//catch all data attributes
		@category [data-v-taxonomy_item-*] = $taxonomy_itemItem['@@__data-v-taxonomy_item-([a-zA-Z_]+)__@@']
		
		@category [data-v-taxonomy_item-url]|href = <?php echo htmlentities(Vvveb\url('product/taxonomy_item/index', $taxonomy_itemItem));?>
		@category [data-v-taxonomy_item-img]|src = $taxonomy_itemItem['images'][0]
				
		@category|append = <?php 
		
		 $generate_menu($taxonomy_itemItem['taxonomy_item_id'], $_categories);
	}?>
	
	@category|after = 
	<?php } ?>

	@categories|after = <?php 
}; 
reset($_categories);
$generate_menu($_categories[key($_categories)]['parent_id'], $_categories); }
?>


