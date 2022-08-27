//set selector prefix to have shorter and easier to read selectors for rules
@posts = [data-v-component-posts]
@post  = [data-v-component-posts] [data-v-post]

//editor info
@post|data-v-id = $post['post_id']
@post|data-v-type = 'post'

@posts|prepend = <?php
	if (isset($_posts_idx)) $_posts_idx++; else $_posts_idx = 0;
	$previous_component = isset($current_component)?$current_component:null;
	$posts = $current_component = $this->posts[$_posts_idx] ?? [];

	$_pagination_count = $posts['count'] ?? 0;
	$_pagination_limit = isset($posts['limit']) ? $posts['limit'] : 5;
?>

@post|deleteAllButFirstChild

@post|before = <?php 
//if no posts available and page is loaded in editor then set an empty post to show post content for the editor
$_default = (isset($vvveb_is_page_edit) && $vvveb_is_page_edit ) ? [0 => []] : [];
$_default = [0 => []];
$_posts = empty($posts['posts']) ? $_default : $posts['posts'];
//$pagination = $this->posts[$_posts_idx]['pagination'];

foreach ($_posts as $index => $post) 
{ 
	$post['image'] = $post['images'][0] ?? '';
?>

	//editor attributes


    //catch all data attributes
    @post [data-v-post-*]|innerText = $post['@@__data-v-post-([a-zA-Z_]+)__@@']
    @post img[data-v-post-*]|src = $post['@@__data-v-post-([a-zA-Z_]+)__@@']
	
	@post [data-v-post-excerpt] = <?php 
		if (isset($post['excerpt']) && !empty($post['excerpt'])) echo $post['excerpt'];
	?>

	//@post [data-v-post-img]|src = <?php echo $post['images'][0] ?? '';?>
	
	@post [data-v-post-url]|href =<?php echo htmlentities(Vvveb\url('content/post/index', $post));?>
	@post [data-v-post-url]|title = $post['title']	
	
	@post [data-v-post-content] = <?php if (isset($post['content'])) echo $post['content'];?>
	
	@post|after = <?php 
}

$current_component = $previous_component;
?>