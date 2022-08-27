[data-v-component-reviews] [data-v-review]|deleteAllButFirstChild

[data-v-component-reviews]|prepend = <?php
if (isset($_reviews_idx)) $_reviews_idx++; else $_reviews_idx = 0;

//$_pagination_count = $this->reviews[$_reviews_idx]['count'];
//$_pagination_limit = $this->reviews[$_reviews_idx]['limit'];
?>


[data-v-component-reviews]  [data-v-review]|before = <?php
if(isset($this->reviews) && is_array($this->reviews[$_reviews_idx]['reviews'])) 
{
	//$pagination = $this->reviews[$_reviews_idx]['pagination'];
	foreach ($this->reviews[$_reviews_idx]['reviews'] as $index => $review) 
	{
		//var_dump($review);
	?>
	
	[data-v-component-reviews] [data-v-review] [data-v-review-*] = 
    <?php 
        if (isset($review['@@__[data-v-review-*]:data-v-review-([a-zA-Z_]+)__@@'])) 
            echo $review['@@__[data-v-review-*]:data-v-review-([a-zA-Z_]+)__@@'];
    ?>
    
    [data-v-component-reviews] [data-v-review] [data-v-review-url] = <?php 
        echo Vvveb\url(['module' => 'review/review', 'review_id' => $review['review_id']]);
    ?>
	
	[data-v-component-reviews]  [data-v-review]|after = <?php 
	} 
}
?>
