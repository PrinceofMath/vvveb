import(common.tpl)
import(pagination.tpl)

[data-v-orders] [data-v-order]|deleteAllButFirstChild

[data-v-orders]  [data-v-order]|before = <?php
if(isset($this->orders) && is_array($this->orders)) {
	//$pagination = $this->orders[$_orders_idx]['pagination'];
	foreach ($this->orders as $index => $order) {?>
    
    [data-v-orders] [data-v-order] [data-v-order-url]|href = <?php echo Vvveb\url(['module' => 'order/order', 'order_id' => $order['order_id']]);?>
	
	[data-v-orders] [data-v-order] [data-v-*]|innerText = $order['@@__data-v-([a-zA-Z_]+)__@@']

	[data-v-orders]  [data-v-order]|after = <?php 
	} 
}?>



