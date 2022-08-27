import(common.tpl)

[data-v-language-list] option|deleteAllButFirst

[data-v-language-list] option|before = <?php
	foreach ($this->languagesList as $code => $language) {
?>

	[data-v-language-list] option img|src = <?php 
			$code = $language['code'];
			echo '/img/flags/' . $code . '.png';
	?>	
	
	[data-v-language-list] option|style = <?php 
			$code = $language['code'];
			echo 'background-image:url(/img/flags/' . $code . '.png)';
	?>
	
	[data-v-language-list] option|value = $code
	[data-v-language-list] option = <?php 
		if (isset($language['emoji'])) {
			echo $language['emoji'] . ' ';
		}
		echo $language['name'];
	?>

[data-v-language-list] option|after = <?php 
} ?>

.list-group li|deleteAllButFirst

.list-group li|before = <?php
	foreach ($this->languages as $code => $language) {
?>

	.list-group li img|src = <?php 
			$code = $language['code'];
			echo '/img/flags/' . $code . '.png';
	?>
	
	.list-group li [data-v-language-default]|if_exists =$language['default']
	.list-group li [data-v-language-name] = $language['name']

.list-group li|after = <?php 
} ?>
