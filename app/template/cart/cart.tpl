import(common.tpl)

[data-v-cart] [data-v-cart-product]|deleteAllButFirstChild

[data-v-cart] [data-v-cart-product]|before = <?php
$products  = $this->cart['products'];
if(is_array($this->products)) foreach ($products as $index => $product) {
?>

	//[data-v-cart] [data-v-cart-product] [data-v-cart-product-name] = $product['name']
	//[data-v-cart] [data-v-cart-product] [data-v-cart-product-description] = $product['description']
	//[data-v-cart] [data-v-cart-product] [data-v-cart-product-amount] = <?php echo $product['amount'];?>

	[data-v-cart] [data-v-cart-product] [data-v-cart-product-url]|href = <?php echo Vvveb\url('product/product/index', $product);?>

	[data-v-cart] [data-v-cart-product] [data-v-cart-product-img]|src = $product['images'][0]

	//catch all data attributes
	[data-v-cart] [data-v-cart-product] [data-v-cart-product-*]|innerText = <?php echo Vvveb\escHtml( $product['@@__data-v-cart-product-([a-zA-Z_]+)__@@'] )?>
	[data-v-cart] [data-v-cart-product] input[data-v-cart-product-*]|value = <?php echo Vvveb\escAttr( $product['@@__data-v-cart-product-([a-zA-Z_]+)__@@'] )?>

[data-v-cart] [data-v-cart-product]|after = <?php }?>


[data-v-cart-*]|innerText = <?php echo Vvveb\escHtml( $this->cart['@@__data-v-cart-([a-zA-Z_]+)__@@'] )?>
