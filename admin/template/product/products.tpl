import(common.tpl)
import(pagination.tpl)


[data-v-products] [data-v-product]|deleteAllButFirstChild


[data-v-products]  [data-v-product]|before = <?php
if(isset($this->products) && is_array($this->products)) 
{
	//$pagination = $this->products[$_products_idx]['pagination'];
	foreach ($this->products as $index => $product) 
	{
	?>
	
	[data-v-products] [data-v-product] [data-v-*]|title = $product['@@__data-v-([a-zA-Z_-]+)__@@']
	[data-v-products] [data-v-product] [data-v-*]|innerText = $product['@@__data-v-([a-zA-Z_-]+)__@@']	
	
	[data-v-products] [data-v-product] a[data-v-*]|href = $product['@@__data-v-([a-zA-Z_-]+)__@@']	

/*
	[data-v-products] [data-v-product] [data-v-name] = $product['name']
	[data-v-products] [data-v-product] [data-v-description] = $product['description']
	[data-v-products] [data-v-product] [data-v-warranty] = $product['warranty']
	[data-v-products] [data-v-product] [data-v-stock] = $product['stock']
	[data-v-products] [data-v-product] [data-v-sku] = $product['sku']
	[data-v-products] [data-v-product] [data-v-weight] = $product['weight']
	[data-v-products] [data-v-product] [data-v-sales] = $product['sales']
	[data-v-products] [data-v-product] [data-v-id] = $product['id']

	[data-v-products] [data-v-product] [data-v-price] = $product['price']
	[data-v-products] [data-v-product] [data-v-promotional_price] = $product['promotional_price']
	[data-v-products] [data-v-product] [data-v-selling_price] = $product['selling_price']
*/

	[data-v-products] [data-v-product] [data-v-img]|src = $product['image']
/*	
	<?php 
		echo '/image/' .$product['image'];
		//echo htmlentities(str_replace('large', '@@__class:image_([a-zA-Z_]+)__@@', $product['images'][$product['main_image']]['url']));
	?>
*/
	
	[data-v-products] [data-v-product] [data-v-product-cart-url]|href = <?php echo Vvveb\url(['module' => 'product/product', 'product_id' => $product['product_id']]);?>
	
	[data-v-products] [data-v-product] [data-v-url]|href =<?php echo Vvveb\url(['module' => 'product/product', 'product_id' => $product['product_id']]);?>
	[data-v-products] [data-v-product] [data-v-url]|title = $product['title']	
	
	[data-v-products] [data-v-product] [data-v-category] = $product['category'];
	[data-v-products] [data-v-product] [data-v-manufacturer] = $product['manufacturer'];
	
	
	[data-v-products]  [data-v-product]|after = <?php 
	} 
}?>
