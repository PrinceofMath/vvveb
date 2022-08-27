import(common.tpl)


/* input elements */

[data-v-product] input[name]|value = 
<?php
	$name = '@@__name__@@';
	$value = '@@__value__@@';
	
	 if (isset($_POST[$name])) 
		echo $_POST[$name]; 
	 else if (isset($this->product[$name])) 
		echo $this->product[$name];
	 else echo $value;
?>

/*
[data-v-product] textarea[name] = 
<?php
	$name = $name;
	 if (isset($_POST[$name])) 
		echo $_POST[$name]; 
	 else if (isset($this->product[$name])) 
		echo $this->product[$name];
?>
*/

/*
[data-v-product] input[data-v-product-*]|value =
<?php
	$name = '@@__data-v-product-([a-zA-Z_]+)__@@';
	$value = '@@__value__@@';

	 if (isset($_POST[$name])) 
		echo $_POST[$name]; 
	 elseif (isset($this->product[$name])) 
		echo $this->product[$name];
	else echo $value;
?>

*/

/* textarea elements */
[data-v-product] textarea[data-v-product-*] = 
<?php
	$name = '@@__data-v-product-([a-zA-Z_]+)__@@';
	$value = '@@__value__@@';
	
	 if (isset($_POST[$name])) 
		echo $_POST[$name]; 
	 else if (isset($this->product[$name])) 
		echo $this->product[$name];
	else echo '@@__value__@@';
?>


[data-v-product] select[data-v-product-*]|before = 
<?php
	 $selected = '';	
	 $name = '@@__data-v-product-([a-zA-Z_]+)__@@';
	 if (isset($_POST[$name])) {
		 $selected = $_POST[$name];
	 } else
	 if (isset($this->product[$name])) {
		$selected = $this->product[$name];
	 }
?>


[data-v-product] select[data-v-product-*] [data-v-option]|deleteAllButFirstChild

[data-v-product] select[data-v-product-*] [data-v-option]|before = <?php
	if (isset($this->$name)) {
	$options = 	$this->$name;
	foreach($options as $key => $option){?>
	
		[data-v-product] select[data-v-product-*] [data-v-option]|value = $option
		[data-v-product] select[data-v-product-*] [data-v-option] = <?php echo ucfirst($option);?>

[data-v-product] select[data-v-product-*] [data-v-option]|after = <?php
}}?>

[data-v-product] select[data-v-product-*] [data-v-option]|addNewAttribute = <?php if ($option == $selected) echo 'selected';?>

/* template */
@templates-select-option = [data-v-product] select[data-v-product-templates] [data-v-option]

@templates-select-option|before = <?php
if ($optgroup != $option['folder']) {
	$optgroup = $option['folder'];
	echo '<optgroup label="' . ucfirst($optgroup) . '">';
}
?>

@templates-select-option|after = <?php
if ($optgroup != $option['folder']) {
	$optgroup = $option['folder'];
	echo "/<optgroup>";
}
?>

@templates-select-option|value = <?php echo $option['file'];?>
@templates-select-option|addNewAttribute = <?php if ($option['file']== $this->product['template']) echo 'selected';?>
@templates-select-option = <?php echo ucfirst($option['title']);?>


/* category */

[data-v-product-categories] [data-v-product-category]|deleteAllButFirstChild 

[data-v-product-categories] [data-v-product-category]|before = 
<?php
	if (isset($this->product_categories))
	foreach($this->product_categories as $category)
	{
?>

	[data-v-product-category-name] = <?php echo $category['name'];?>

[data-v-product-categories] [data-v-product-category]|after = <?php 
	}
?>



/* language tabs */
[data-v-languages]|before = <?php $_lang_instance = '@@__data-v-languages__@@';$_i = 0;?>
[data-v-languages] [data-v-language]|deleteAllButFirstChild
//[data-v-languages] [data-v-language]|addClass = <?php if ($_i == 0) echo 'active';?>

[data-v-languages] [data-v-language]|before = <?php
foreach ($this->languagesList as $language) 
{
?>
	[data-v-languages] [data-v-language-id]|id = <?php echo 'lang-' . $language['code'] . '-' . $_lang_instance;?>
	[data-v-languages]  [data-v-language-id]|addClass = <?php if ($_i == 0) echo 'show active';?>

	[data-v-languages] [data-v-language] [data-v-language-name] = $language['name']
	[data-v-languages] [data-v-language] [data-v-language-img]|title = $language['name']
	[data-v-languages] [data-v-language] [data-v-language-img]|src = <?php echo 'language/' . $language['code'] . '/' . $language['code'] . '.png';?>
	[data-v-languages] [data-v-language] [data-v-language-link]|href = <?php echo '#lang-' . $language['code'] . '-' . $_lang_instance?>
	[data-v-languages] [data-v-language] [data-v-language-link]|addClass = <?php if ($_i == 0) echo 'active';?>

[data-v-languages] [data-v-language]|after = <?php 
$_i++;
}
?>


[data-v-product] input[data-v-product-description-*]|name = <?php echo 'product_description[' . $language['language_id'] . '][@@__data-v-product-description-([a-zA-Z_]+)__@@]'; ?>
[data-v-product] textarea[data-v-product-description-*]|name = <?php echo 'product_description[' . $language['language_id'] . '][@@__data-v-product-description-([a-zA-Z_]+)__@@]'; ?>

[data-v-product] input[data-v-product-description-*]|value = <?php
	if (isset($this->product['product_description'][$language['language_id']]['@@__data-v-product-description-([a-zA-Z_]+)__@@'])) 
		echo $this->product['product_description'][$language['language_id']]['@@__data-v-product-description-([a-zA-Z_]+)__@@'];
?>

[data-v-product] textarea[data-v-product-description-*] = <?php
	if (isset($this->product['product_description'][$language['language_id']]['@@__data-v-product-description-([a-zA-Z_]+)__@@'])) 
		echo $this->product['product_description'][$language['language_id']]['@@__data-v-product-description-([a-zA-Z_]+)__@@'];
?>

[data-v-product] input[data-v-product-description-language_id]|value = <?php echo $language['language_id']; ?>


[data-v-product] [data-v-image]|data-v-image = $this->product['image_url']
[data-v-product] input[data-v-image]|value = $this->product['image']
[data-v-product] [data-v-image]|src = <?php echo $this->product['image_url'] ? $this->product['image_url'] : 'img/placeholder.svg';?>

//image gallery
[data-v-product] [data-v-images] [data-v-image]|deleteAllButFirst

[data-v-product] [data-v-images] [data-v-image]|before = <?php
if(isset($this->product['images']) && is_array($this->product['images']))
foreach ($this->product['images'] as $product_image_id => $image) 
{
?>

	[data-v-product] [data-v-images] [data-v-image-src]|src = $image['image']

	[data-v-product] [data-v-images] [data-v-image]|after = <?php 
}
?>


[data-v-product] [data-v-add_to_cart]|href = <?php echo htmlentities(Vvveb\url(['module' => 'checkout/cart', 'product_id' => $product['product_id']]));?>


//attributes

[data-v-product] [data-v-attributes] [data-v-group]|deleteAllButFirst
[data-v-product] [data-v-attributes] [data-v-attribute]|deleteAllButFirst

 
[data-v-product] [data-v-url]|href = $this->product['url']
[data-v-product] [data-v-url] = $this->product['url']

[data-v-product] [data-v-design_url]|href = $this->product['design_url']


import(product/product_taxonomy_item.tpl)
