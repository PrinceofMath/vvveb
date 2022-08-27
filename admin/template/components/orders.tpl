@order = [data-v-component-orders] [data-v-order]
@order|deleteAllButFirstChild

[data-v-component-orders]|prepend = <?php
if (isset($_orders_idx)) $_orders_idx++; else $_orders_idx = 0;

$orders = [];

if(isset($this->orders) && is_array($this->orders[$_orders_idx]['orders'])) 
{
	$orders = $this->orders[$_orders_idx];
}

//$_pagination_count = $this->orders[$_orders_idx]['count'];
//$_pagination_limit = $this->orders[$_orders_idx]['limit'];
?>

[data-v-component-orders] [data-v-orders-*]|innerText = $orders['@@__data-v-orders-([a-zA-Z_]+)__@@']

@order|before = <?php
if($orders) {
	//$pagination = $this->orders[$_orders_idx]['pagination'];
	foreach ($orders['orders'] as $index => $order) {?>
	
	@order [data-v-order-*]|innerText = $order['@@__data-v-order-([a-zA-Z_]+)__@@']
	@order [data-v-order-*]|title = $order['@@__data-v-order-([a-zA-Z_]+)__@@']
    
    @order [data-v-order-url]|href = <?php echo Vvveb\url(['module' => 'order/order', 'order_id' => $order['order_id']]);?>
	
	@order|after = <?php 
	} 
}
?>


