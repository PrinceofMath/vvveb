//set selector prefix to have shorter and easier to read selectors for rules
@products = [data-v-component-products]
@product  = [data-v-component-products] [data-v-product]
@product-image = [data-v-product-images] [data-v-product-image]

//we only need the first product element to iterate
@product|deleteAllButFirstChild

//set component variables
@products|prepend = <?php
	//use a counter to know which component instance we need to use if there are more than one component on page
	if (isset($_products_idx)) $_products_idx++; else $_products_idx = 0;
	$previous_component = isset($current_component)?$current_component:null;
	$products = $current_component = $this->products[$_products_idx] ?? [];

	$_pagination_count = $products['count'];
	$_pagination_limit = isset($products['limit'])? $products['limit'] : 5;
?>

@products [data-v-category] = <?php $_category = current($products['products']);echo $_category['category'];?>
@products [data-v-manufacturer] = <?php $_manufacturer = current($products['products']);echo $_manufacturer['manufacturer'];?>


@product|before = <?php
//if page loaded in editor then set a fist empty product if there are no products 
//to render an empty product to avoid losing the html on edit

$_default = (isset($vvveb_is_page_edit) && $vvveb_is_page_edit ) ? [0 => []] : [];
$_default = [0 => []];
$_products = empty($products['products']) ? $_default : $products['products'];

foreach ($_products as $index => $product)  { 
?>
	//editor attributes
	@product|data-v-id = $product['product_id']
	@product|data-v-type = 'product'
	
	//title attributes
	@product [data-v-product-title]|title = $product['name']	
	@product [data-v-product-alt]|alt = $product['name']	
	
	@product [data-product_id]|data-product_id = $product['product_id']

	//url
	@product [data-v-product-url]|title = $product['title']	
	@product [data-v-product-url]|href =<?php 
			
		$url = '@@__data-v-product-url__@@';	
		if (!$url) $url = 'product/product/index';
		echo htmlentities(Vvveb\url($url, $product));
	?>

	//image
	@product [data-v-product-image]|src = $product['images'][0]['image']
	@product [data-v-product-image]|data-v-id = $product['images'][0]['id']
	@product [data-v-product-image]|data-v-type = 'product_image'
	
	//usually used for second image to show hover
	@product [data-v-product-image-*]|src = $product['images']['@@__data-v-product-image-(\d+)__@@']['image']
	@product [data-v-product-image-*]|data-v-id = $product['images']['@@__data-v-product-image-(\d+)__@@']['id']
	@product [data-v-product-image-*]|data-v-type = 'product_image'
	
	//image gallery
	@product [data-v-product-images] [data-v-product-image]|before = <?php
	if(isset($product['images']) && is_array($product['images']))
	foreach ($product['images'] as $product_image_id => $image)  {
	?>

		@product [data-v-product-images] img[data-v-product-image-src]|src = $image['image']
		@product [data-v-product-images] img[data-v-product-image-src]|data-v-id  = $product_image_id
		@product [data-v-product-images] img[data-v-product-image-src]|data-v-type = 'product_image'
		@product [data-v-product-images] a[data-v-product-image-src]|href = $image['image']
		
		@product [data-v-product-images] [data-v-product-image]|after = <?php 
	} ?>

    //catch all data attributes
    @product [data-v-product-*]|innerText = $product['@@__data-v-product-([a-zA-Z_]+)__@@']
	//echo description directly to avoid htmlentities escape
	@product [data-v-product-description] = <?php echo $product['description'];?>	
	
	@product|after = <?php 
}

$current_component = $previous_component;
?>