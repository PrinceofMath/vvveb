import(common.tpl)

[data-v-theme-list] option|deleteAllButFirst

[data-v-theme-list] option|before = <?php
	foreach ($this->themeList as $code => $theme) {
?>

	[data-v-theme-list] option|value = $code
	[data-v-theme-list] option|addNewAttribute = <?php if ($code == $this->site['theme']) echo 'selected';?>
	[data-v-theme-list] option = <?php 
		echo $theme['name'];
	?>

[data-v-theme-list] option|after = <?php 
} ?>


@templates-select-option = [data-v-template-list] [data-v-option]

@templates-select-option|deleteAllButFirstChild

@templates-select-option|before = <?php
	$options = 	$this->templateList;
	foreach($options as $key => $option){?>
	
		@templates-select-option|value = $option
		@templates-select-option = <?php echo ucfirst($option);?>

@templates-select-option|after = <?php
}?>

@templates-select-option|addNewAttribute = <?php if ($option == $selected) echo 'selected';?>


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
@templates-select-option|addNewAttribute = <?php if ($option['file']== $this->site['home_template']) echo 'selected';?>
@templates-select-option = <?php echo ucfirst($option['title']);?>


input[data-v-site-*]|value = <?php
	$name = '@@__data-v-site-([a-zA-Z_]+)__@@';
	 if (isset($_POST['site'][$name])) 
		echo $_POST['site'][$name]; 
	 else if (isset($this->site[$name])) 
		echo $this->site[$name];
?>		

[data-v-site-*]|innerText = $this->site['@@__data-v-site-([a-zA-Z_]+)__@@']
[data-v-site-*]|title = $this->site['@@__data-v-site-([a-zA-Z_]+)__@@']
a[data-v-site-*]|title|href = $this->site['@@__data-v-site-([a-zA-Z_-]+)__@@']