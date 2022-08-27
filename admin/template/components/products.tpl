@product = [data-v-component-products] [data-v-product]
@product|deleteAllButFirstChild

[data-v-component-products]|prepend = <?php
if (isset($_products_idx)) $_products_idx++; else $_products_idx = 0;

$_pagination_count = $this->products[$_products_idx]['count'];
$_pagination_limit = isset($this->products[$_products_idx]['limit'])? $this->products[$_products_idx]['limit'] : 5;
?>

[data-v-component-products] [data-v-category] = <?php $_category = current($this->products[$_products_idx]['products']);echo $_category['category'];?>
[data-v-component-products] [data-v-manufacturer] = <?php $_manufacturer = current($this->products[$_products_idx]['products']);echo $_manufacturer['manufacturer'];?>


[data-v-component-products]  [data-v-product]|before = <?php
if(isset($this->products) && is_array($this->products[$_products_idx]['products'])) 
{
	//$pagination = $this->products[$_products_idx]['pagination'];
	foreach ($this->products[$_products_idx]['products'] as $index => $product) 
	{
?>

	// @product [data-v-product-name] = $product['name']
	// @product [data-v-description] = $product['description']
	// @product [data-v-warranty] = $product['warranty']
	// @product [data-v-stock] = $product['stock']
	// @product [data-v-sku] = $product['sku']
	// @product [data-v-weight] = $product['weight']
	// @product [data-v-sales] = $product['sales']
	// @product [data-v-product-id]|data-v-product-id = $product['product_id']	

	// @product [data-v-price] = $product['price']
	// @product [data-v-promotional_price] = $product['promotional_price']
	// @product [data-v-selling_price] = $product['selling_price']
	// @product [data-v-category] = $product['category'];
	// @product [data-v-manufacturer] = $product['manufacturer'];

	@product [data-v-product-img]|src = $product['images'][0]

	@product [data-v-img-hover]|if_exists = $product['images'][1]
	@product [data-v-img-hover]|src = $product['images'][1]
	
	
    //catch all data attributes
    @product [data-v-product-*]|innerText = $product['@@__data-v-product-([a-zA-Z_]+)__@@']
	
	@product [data-v-product-description] = <?php echo $product['description'];?>
	
    
	@product [data-v-product-cart-url]|href = <?php echo htmlentities(Vvveb\url('checkout/cart' ,$product));?>
	@product [data-v-product-cart-url]|data-v-product_id = $product['product_id']
	
	@product [data-v-product-url]|href =<?php echo htmlentities(Vvveb\url('product/product/index', $product));?>
	@product [data-v-product-url]|title = $product['title']	
	
	@product|after = <?php 
	} 
}?>
