@customer = [data-v-component-customers] [data-v-customer]
@customer|deleteAllButFirstChild

[data-v-component-customers]|prepend = <?php
if (isset($_customers_idx)) $_customers_idx++; else $_customers_idx = 0;

$customers = [];

if(isset($this->customers) && is_array($this->customers[$_customers_idx]['customers'])) 
{
	$customers = $this->customers[$_customers_idx];
}

//$_pagination_count = $this->customers[$_customers_idx]['count'];
//$_pagination_limit = $this->customers[$_customers_idx]['limit'];
?>

[data-v-component-customers] [data-v-customers-*]|innerText = $customers['@@__data-v-customers-([a-zA-Z_]+)__@@']

@customer|before = <?php
if($customers) {
	//$pagination = $this->customers[$_customers_idx]['pagination'];
	foreach ($customers['customers'] as $index => $customer) {
	?>
	
	@customer [data-v-customer-*]|innerText = $customer['@@__data-v-customer-([a-zA-Z_]+)__@@']
	@customer [data-v-customer-*]|title = $customer['@@__data-v-customer-([a-zA-Z_]+)__@@']
    
    @customer [data-v-customer-url]|href = <?php echo Vvveb\url(['module' => 'customer/customer', 'customer_id' => $customer['customer_id']]);?>
	
	@customer|after = <?php 
	} 
}
?>