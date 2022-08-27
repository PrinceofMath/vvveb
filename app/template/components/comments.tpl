@comments = [data-v-component-comments]
@comment = [data-v-component-comments] [data-v-comment]

@comment|deleteAllButFirstChild

@comments|prepend = <?php
	if (isset($_comments_counter)) $_comments_counter++; else $_comments_counter = 0;
?>


@comments  [data-v-comment]|before = <?php
if(isset($this->comments) && is_array($this->comments[$_comments_counter]['comments'])) {
	foreach ($this->comments[$_comments_counter]['comments'] as $index => $comment)  {?>
		
		@comment|data-comment_id = $comment['comment_id']
		
		@comment [data-v-comment-content] = <?php echo $comment['content'];?>
		
		@comment [data-v-comment-avatar]|src = $comment['avatar']
		
		@comment [data-v-comment-*]|innerText = $comment['@@__data-v-comment-([a-zA-Z_]+)__@@']
		
		
		@comment [data-v-comment-url] = <?php 
			echo Vvveb\url(['module' => 'comment/comment', 'comment_id' => $comment['comment_id']]);
		?>
	
	@comments  [data-v-comment]|after = <?php 
	} 
}
?>
