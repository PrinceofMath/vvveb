@currencies = [data-v-component-currency]
@currency = [data-v-component-currency] [data-v-currency]

@currency|deleteAllButFirstChild

@currencies|prepend = <?php
if (isset($_currency_idx)) $_currency_idx++; else $_currency_idx = 0;
$currency = $current_component = $this->currency[$_currency_idx];
$currencies = $currency['currencies'] ?? []; 
?>


@currency|before = <?php
if($currencies)  {
	foreach ( $currencies as $index => $currency) { ?>
	@currency [data-v-currency-*]|innerText = $currency['@@__data-v-currency-([a-zA-Z_]+)__@@']
	
    @currency [data-v-currency-url] = <?php 
        echo Vvveb\url(['module' => 'currency/currency', 'currency_id' => $currency['currency_id']]);
    ?>
	
	@currency|after = <?php 
	} 
}
?>
