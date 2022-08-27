@product = [data-v-component-product]
@images = [data-v-component-product] [data-v-product-images]


@product|data-v-id = $product['product_id']
@product|data-v-type = 'product'

@product|before = <?php
	if (isset($_product_idx)) $_product_idx++; else $_product_idx = 0;
	$previous_component = $component;
	$product = $current_component = $this->product[$_product_idx];
?>

//editor attributes
@product|data-v-id = $product['product_id']
@product|data-v-type = 'product'

//catch all data attributes
@product [data-v-product-*]|innerText = $product['@@__data-v-product-([a-zA-Z_]+)__@@']
@product input[data-v-product-*]|value = $product['@@__data-v-product-([a-zA-Z_]+)__@@']


//manual echo to avoid html escape
@product [data-v-product-description] = <?php echo $product['description'];?>

@product [data-v-product-manufacturer_url]|href = <?php 
	echo url('manufacturer', '', ['id_manufacturer' => $product['id_manufacturer'], 'manufacturer' => $product['manufacturer']]);
?>


@product img[data-v-product-main-image]|src = <?php echo reset($product['images'])['image'];?>
@product a[data-v-product-main-image]|href = <?php echo reset($product['images'])['image'];?>


@images [data-v-product-image]|deleteAllButFirstChild

@product [data-v-add_to_cart]|href = <?php 
	echo htmlentities(Vvveb\url(['module' => 'checkout/cart', 'product_id' => $product['product_id']]));
?>


@images [data-v-product-image]|before = <?php
if(isset($product['images']) && is_array($product['images']))
	foreach ($product['images'] as $product_image_id => $image)  { ?>

		@images img[data-v-product-image-src]|src = $image['image']
		@images a[data-v-product-image-src]|href = $image['image']
		
		@images [data-v-product-image]|after = <?php 
}

$component = $previous_component;
?>