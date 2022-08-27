/*
Translations
*/

//[data-translate] = <?php echo __('@@__innerHTML__@@'); ?>
[data-translate]|innerText = <?php 
	$_text = '@@macro getTranslateText()@@';
	if (function_exists('__')) echo __($_text); else echo $_text; 
?>
input[data-translate]|value = <?php if (function_exists('__')) echo __('@@__value__@@'); else echo '@@__value__@@'; ?>
input[data-translate]|placeholder = <?php if (function_exists('__')) echo __('@@__placeholder__@@'); else echo '@@__placeholder__@@'; ?>
img[data-translate]|alt = <?php if (function_exists('__')) echo __('@@__alt__@@'); else echo '@@__alt__@@'; ?>

