import(common.tpl)
import(pagination.tpl)


[data-v-posts] [data-v-post]|deleteAllButFirstChild

[data-v-posts]  [data-v-post]|before = <?php
if(isset($this->posts) && is_array($this->posts)) {
	//$pagination = $this->posts[$_posts_idx]['pagination'];
	foreach ($this->posts as $index => $post) { ?>
	
	[data-v-posts] [data-v-post] [data-v-*]|innerText = $post['@@__data-v-([a-zA-Z_]+)__@@']
	[data-v-posts] [data-v-post] [data-v-*]|title = $post['@@__data-v-([a-zA-Z_]+)__@@']

	[data-v-posts] [data-v-post] [data-v-img]|src =  <?php echo $post['image'] ? $post['image']: 'img/placeholder.svg';?>
	[data-v-posts] [data-v-post] [data-v-url]|title = $post['name']	
	
	[data-v-posts] [data-v-post] a[data-v-*]|href = $post['@@__data-v-([a-zA-Z_-]+)__@@']	
	
	[data-v-posts]  [data-v-post]|after = <?php 
	} 
}?>