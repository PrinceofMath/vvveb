//example class="copy_from_index.html"
.copy_from*|outerHTML = from(@@__class:copy_from:([^\,]+)__@@|@@__class:copy_from:[^\,]+\,([^\,]+)__@@)


/* modifiers */
.capitalize|register_filter = <?php ucfirst($content, $arg1, $arg2);?>


.if_*|after = <?php } ?>

.if_*|before = 
<?php if (@@macro if('@@__class__@@')@@) {?> 

[class*=":if_"]|addClass =  <?php @@macro class_if('@@__class__@@')@@?>
					  
//display global variables
.store_* = 
<?php 
	if (isset($this->@@__class:store_([a-zA-Z_]+)__@@)) 
		echo htmlentities($this->@@__class:store_([a-zA-Z_]+)__@@);
?>

					  
/*body|prepend = <?php var_dump($this);?>*/

head base|href = <?php echo Vvveb\themeUrlPath()?>;



input[type="text"]|value = 
<?php
	 if (isset($_POST['@@__name__@@'])) 
		echo $_POST['@@__name__@@'];
	else echo '@@__value__@@';		
?>



input[type="password"]|value = 
<?php
	 if (isset($_POST['@@__name__@@'])) 
		echo $_POST['@@__name__@@']; 
	else echo '@@__value__@@';
?>


input[type="email"]|value = 
<?php
	 if (isset($_POST['@@__name__@@'])) 
		echo $_POST['@@__name__@@'];
	else echo '@@__value__@@';		
?>


input[type="checkbox"]|value = 
<?php
	 if (isset($_POST['@@__name__@@'])) 
		echo $_POST['@@__name__@@'];
	 else echo '@@__value__@@';		
?>

/*
input[type="checkbox"]|checked = 
<?php
	 if (isset($_POST['@@__name__@@']) && $_POST['@@__name__@@']) 
		echo $_POST['@@__value__@@']; 
	else echo 'false';
?>
*/


[data-v-errors]|if_exists = $this->errors

[data-v-errors] [data-v-error]|deleteAllButFirstChild


[data-v-errors]  [data-v-error]|before = <?php 
if(isset($this->errors) && is_array($this->errors)) 
{
	foreach ($this->errors as $error) 
	{
	?>
	
	[data-v-errors] [data-v-error] [data-v-error-text] = <?php echo '<p>' . str_replace("\n", '</p><p>', $error) . '</p>';?>
	
	[data-v-errors]  [data-v-error]|after = <?php 
	} 
}?>



