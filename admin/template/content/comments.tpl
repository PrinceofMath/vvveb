import(common.tpl)
import(pagination.tpl)

@comment = [data-v-comments] [data-v-comment]

@comment|deleteAllButFirstChild

@comment|before = <?php
if(isset($this->comments) && is_array($this->comments))  {
	foreach ($this->comments as $index => $comment) {?>
	
		@comment [data-v-comment-*]|innerText = $comment['@@__data-v-comment-([a-zA-Z_]+)__@@']
		@comment [data-v-comment-*]|title = $comment['@@__data-v-comment-([a-zA-Z_]+)__@@']

		@comment [data-v-img]|src = $comment['image']
		@comment [data-v-url]|title = $comment['name']	
		
		@comment a[data-v-comment-*]|href = $comment['@@__data-v-comment-([a-zA-Z_-]+)__@@']	
		
	@comment|after = <?php } 
}?>



