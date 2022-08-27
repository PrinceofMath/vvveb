@article = [data-v-component-articles] [data-v-article]
@article|deleteAllButFirstChild

[data-v-component-articles]|prepend = <?php
if (isset($_articles_idx)) $_articles_idx++; else $_articles_idx = 0;

$_pagination_count = $this->articles[$_articles_idx]['count'];
$_pagination_limit = $this->articles[$_articles_idx]['limit'];
?>


[data-v-component-articles]  [data-v-article]|before = <?php
if(isset($this->articles) && is_array($this->articles[$_articles_idx]['articles'])) 
{
	//$pagination = $this->articles[$_articles_idx]['pagination'];
	foreach ($this->articles[$_articles_idx]['articles'] as $index => $article) {
	?>
	
	@article [data-v-title] = $article['post_title']


	@article [data-v-img]|src = 
	<?php 
		echo $article['image'];
		//echo htmlentities(str_replace('large', '@@__class:image_([a-zA-Z_]+)__@@', $article['images'][$article['main_image']]['url']));
	?>
	
	
	[data-v-component-articles]  [data-v-article]|after = <?php 
	} 
}
?>

