// example <div data-v-copy-from="index.html,#element">
[data-v-copy-from]|outerHTML = from(@@__data-v-copy-from:([^\,]+)__@@|@@__data-v-copy-from:[^\,]+\,([^\,]+)__@@)

/* [data-v-url]|href = <?php var_dump(Vvveb\url(['route' => '@@__data-v-url__@@']));echo Vvveb\url(['route' => '@@__data-v-url__@@']);?> */

[data-v-url]|href = <?php echo htmlentities(Vvveb\url('@@__data-v-url__@@'));?>
form[data-v-url]|action = <?php echo htmlentities(Vvveb\url('@@__data-v-url__@@'));?>

[data-v-url-params]|href = <?php echo Vvveb\url('@@__data-v-url__@@' , @@__data-v-url-params__@@);?>
form[data-v-url-params]|action = <?php echo Vvveb\url('@@__data-v-url__@@' , @@__data-v-url-params__@@);?>

/* [data-v-img]|href = <?php echo htmlentities(Vvveb\url('@@__data-v-img__@@'));?> */

import(components/categories.tpl, [data-v-component-categories])
import(components/menu.tpl, [data-v-component-menu])
import(components/products.tpl, [data-v-component-products])
import(components/posts.tpl, [data-v-component-posts])
import(components/comments.tpl, [data-v-component-comments])
import(components/post.tpl, [data-v-component-post])
import(components/product.tpl, [data-v-component-product])
import(components/filters.tpl, [data-v-component-filters])
import(components/cart.tpl, [data-v-component-cart])
import(components/search.tpl, [data-v-component-search])
import(components/product_gallery.tpl, [data-v-component-product-gallery])
import(components/user.tpl, [data-v-component-user])
import(components/checkout.tpl, [data-v-component-checkout])
import(components/manufacturers.tpl, [data-v-component-manufacturers])
import(components/breadcrumb.tpl, [data-v-component-breadcrumb])
import(components/content/categories.tpl, [data-v-component-content-categories])
import(components/content/archives.tpl, [data-v-component-content-archive])
import(components/currency.tpl, [data-v-component-currency])
import(components/language.tpl, [data-v-component-language])


import(editor.tpl)



/* modifiers */
.capitalize|register_filter = <?php ucfirst($content, $arg1, $arg2);?>


.if_*|after = <?php } ?>

.if_*|before = 
<?php if (@@macro if('@@__class__@@')@@) {?> 

[class*=":if_"]|addClass =  <?php @@macro classIf('@@__class__@@')@@?>
					  
//display global variables
.store_* = 
<?php 
	if (isset($this->@@__class:store_([a-zA-Z_]+)__@@)) 
		echo htmlentities($this->@@__class:store_([a-zA-Z_]+)__@@);
?>

					  
/*body|prepend = <?php var_dump($this);?>*/
head base|href = <?php echo Vvveb\themeUrlPath()?>;


//errors

[data-v-errors]|before = <?php if (isset($this->errors) && is_array($this->errors) && $this->errors) {?>
//[data-v-errors]|if_exists = $this->errors

[data-v-errors] [data-v-error]|deleteAllButFirstChild


[data-v-errors] [data-v-error]|before = <?php 
if(isset($this->errors) && is_array($this->errors)) 
{
	foreach ($this->errors as $error) { ?>
	
		[data-v-errors] [data-v-error] [data-v-error-text]|innerText = $error

	
	[data-v-errors]  [data-v-error]|after = <?php 
	} 
}?>

[data-v-errors]|after = <?php 
	}
?>

//messages

//[data-v-messages]|if_exists = <?php isset($this->messages) && is_array($this->messages) && $this->messages ?>
[data-v-messages]|before = <?php if (isset($this->messages) && is_array($this->messages) && $this->messages) {?>
[data-v-messages]|if_exists = $this->messages

[data-v-messages] [data-v-message]|deleteAllButFirstChild


[data-v-messages] [data-v-message]|before = <?php 
if(isset($this->messages) && is_array($this->messages)) 
{
	foreach ($this->messages as $message)  { ?>
	
		[data-v-messages] [data-v-message] [data-v-message-text]|innerText = $message
	
	[data-v-messages]  [data-v-message]|after = <?php 
	} 
}?>

[data-v-messages]|after = <?php 
	}
?>


//csrf
[data-v-csrf]|value = <?php echo \Vvveb\session('csrf');?>

import(ifmacros.tpl)
