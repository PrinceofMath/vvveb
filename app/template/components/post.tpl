@post = [data-v-component-post]
@image = [data-v-component-post] [data-v-post-images] [data-v-post-image]

//editor info
@post|data-v-id = $_post['post_id']
@post|data-v-type = 'post'

@post|before = <?php

if (isset($_post_idx)) $_post_idx++; else $_post_idx = 0;
$_post = $this->post[$_post_idx];

?>

//catch all data attributes
@post [data-v-post-*]|innerText = $_post['@@__data-v-post-([a-zA-Z_]+)__@@']
@post input[data-v-post-*]|value = $_post['@@__data-v-post-([a-zA-Z_]+)__@@']


//manual echo to avoid html escape
@post [data-v-post-content] = <?php if (isset($_post['content'])) echo $_post['content'];?>
//@post [data-v-post-content] = $_post['content']


@post [data-v-manufacturer_url]|href = 
<?php 
    echo url('manufacturer', '', array('id_manufacturer' => $_post['id_manufacturer'], 'manufacturer' => $_post['manufacturer']));
?>

//featured image
//catch all data attributes
@post [data-v-post-*]|innerText = $_post['@@__data-v-post-([a-zA-Z_]+)__@@']
@post img[data-v-post-*]|src = $_post['@@__data-v-post-([a-zA-Z_]+)__@@']


//images
@image|deleteAllButFirstChild
@image|before = <?php
if(isset($_post['images']) && is_array($_post['images']))
foreach ($_post['images'] as $image) { ?>

	@image [data-v-image-src]|src = <?php 
		echo '/image/' . $image['image'];
	?>

	@image|after = <?php 
}
?>