[data-v-component-checkout]|prepend = <?php 
	if (isset($_user_idx)) $_user_idx++; else $_user_idx = 0;
	$previous_component = isset($component)?$component:null;
	$user = $component = $this->user[$_user_idx];
	//$user = \Vvveb\session('user');
?>


[data-v-component-checkout]|append = <?php 
	$component = $previous_component;
?>




.component_checkout|before = <?php
if (isset($_checkout_idx)) $_checkout_idx++; else $_checkout_idx = 0;
$_payment_first = true;
$_shipping_first = true;
?>

#errorMessage = $this->errorMessage
#errorMessage|style = <?php if(isset($this->errorMessage)) echo 'display:block';?>

//.order_confirmation|if_exists = <?php isset($this->checkout[$_checkout_idx]['order_confirmation'])?>
//.order_confirmation = <?php var_dump($this->checkout[$_checkout_idx]);?>

.component_checkout .order_confirmation|hide = <?php !isset($this->checkout[$_checkout_idx]['order_confirmation'])?>

.component_checkout .payment_options .payment_option|before = 
<?php foreach ($this->checkout[$_checkout_idx]['payment_options'] as $option) {?>


.component_checkout .payment_options .payment_name = $option['name']
.component_checkout .payment_options .payment_instructions = $option['instructions']
.component_checkout .payment_options .payment_option .payment_value|value = $option['id']
.component_checkout .payment_options .payment_option .payment_value|addNewAttribute = <?php if ($_payment_first)  {echo 'checked="true"'; $_payment_first = false;}?>

.component_checkout .payment_options label|for = <?php echo 'payment-' . $option['id'];?>
.component_checkout .payment_options input[type="radio"]|id = <?php echo 'payment-' . $option['id'];?>
  
.component_checkout .payment_options .payment_option|after = <?php } ?> 


.component_checkout .shipping_options .shipping_option|before = 
<?php foreach ($this->checkout[$_checkout_idx]['shipping_options'] as $option) {?>


.component_checkout .shipping_options .shipping_name = $option['name']
.component_checkout .shipping_options .shipping_instructions = $option['instructions']
.component_checkout .shipping_options .shipping_option .shipping_value|value = $option['id']
.component_checkout .shipping_options .shipping_option .shipping_value|addNewAttribute = <?php if ($_shipping_first)  {echo  'checked="true"'; $_shipping_first = false;}?>

.component_checkout .shipping_options label|for = <?php echo 'shipping-' . $option['id'];?>
.component_checkout .shipping_options input[type="radio"]|id = <?php echo 'shipping-' . $option['id'];?>

.component_checkout .shipping_options .shipping_option|after = <?php } ?> 

//set variable for if macro
.component_checkout .if_*.tplttmacroif = 'this->checkout[$_checkout_idx]';

