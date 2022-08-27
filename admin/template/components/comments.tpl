@comment = [data-v-component-comments] [data-v-comment]
@comment|deleteAllButFirstChild

[data-v-component-comments]|prepend = <?php
if (isset($_comments_idx)) $_comments_idx++; else $_comments_idx = 0;

$comments = [];

if(isset($this->comments) && is_array($this->comments[$_comments_idx])) {
	$comments = $this->comments[$_comments_idx];
}
?>

[data-v-component-comments] [data-v-comments-*]|innerText = $comments['@@__data-v-comments-([a-zA-Z_]+)__@@']

@comment|before = <?php
if(isset($comments['comments'])) {
	//$pagination = $this->comments[$_comments_idx]['pagination'];
	foreach ($comments['comments'] as $index => $comment)  {?>
	
	@comment [data-v-comment-*]|innerText = $comment['@@__data-v-comment-([a-zA-Z_]+)__@@']
	@comment [data-v-comment-*]|title = $comment['@@__data-v-comment-([a-zA-Z_]+)__@@']
    
    @comment [data-v-comment-url] = <?php echo Vvveb\url(['module' => 'comment/comment', 'comment_id' => $comment['comment_id']]);?>
	
	@comment|after = <?php 
	} 
}
?>