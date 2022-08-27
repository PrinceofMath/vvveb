@news = [data-v-component-news] [data-v-news]

@news|deleteAllButFirstChild

[data-v-component-news]|prepend = <?php
if (isset($_news_idx)) $_news_idx++; else $_news_idx = 0;

$news = [];
if(isset($this->news) && is_array($this->news[$_news_idx]['news'])) 
{
	$news = $this->news[$_news_idx];
}

//$_pagination_count = $this->news[$_news_idx]['count'];
//$_pagination_limit = $this->news[$_news_idx]['limit'];
?>

[data-v-component-news] [data-v-news-*]|innerText = $news['@@__data-v-news-([a-zA-Z_]+)__@@']

@news|before = <?php
if($news) {
	//$pagination = $this->news[$_news_idx]['pagination'];
	foreach ($news['news'] as $index => $news) {?>
	
	@news [data-v-news-*]|innerText = $news['@@__data-v-news-([a-zA-Z_]+)__@@']
	@news [data-v-news-*]|title = $news['@@__data-v-news-([a-zA-Z_]+)__@@']
    
    @news a[data-v-news-*]|href = $news['@@__data-v-news-([a-zA-Z_]+)__@@']
	
	@news|after = <?php 
	} 
}
?>
