[data-v-component-cart] [data-v-cart-product]|deleteAllButFirstChild

[data-v-component-cart]|prepend = <?php
if (isset($_cart_idx)) $_cart_idx++; else $_cart_idx = 0;
?>

[data-v-component-cart] [data-v-total-items] = $this->cart[$_cart_idx]['total_items']

[data-v-component-cart] [data-v-total] = $this->cart[$_cart_idx]['total']

[data-v-component-cart]  [data-v-cart-product]|before = <?php
$cart = $current_component = $this->cart[$_cart_idx];
$products = $cart['products'] ?? [];

if($products) {
	foreach ($products as $index => $product) {
?>

	//[data-v-component-cart] [data-v-cart-product] [data-v-product-name] = $product['name']
	//[data-v-component-cart] [data-v-cart-product] [data-v-product-price] = $product['price']
	//[data-v-component-cart] [data-v-cart-product] [data-v-product-description] = $product['description']

	//catch all data attributes
	[data-v-component-cart] [data-v-cart-product] [data-v-cart-product-*]|innerText = $product['@@__data-v-cart-product-([a-zA-Z_]+)__@@']

	[data-v-component-cart] [data-v-cart-product] [data-v-cart-product-url]|href = 
		<?php echo htmlentities(Vvveb\url(['module' => 'product', 'product_id' => $product['product_id']]));?>
	[data-v-component-cart] [data-v-cart-product] [data-v-cart-product-remove-url]|href = 
		<?php echo htmlentities(Vvveb\url(['module' => 'cart', 'action' => 'remove', 'product_id' => $product['product_id']]));?>
		
	[data-v-component-cart] [data-v-cart-product]|data-product_id = $product['product_id']		

	[data-v-component-cart] [data-v-cart-product] [data-v-cart-product-img]|src = $product['images'][0]
	


[data-v-component-cart] [data-v-cart-product]|after = <?php } 
}
?>
