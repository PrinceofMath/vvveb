@languages = [data-v-component-language]
@language = [data-v-component-language] [data-v-language]

@language|deleteAllButFirstChild

@languages|prepend = <?php
if (isset($_language_idx)) $_language_idx++; else $_language_idx = 0;
$language = $current_component = $this->language[$_language_idx];
$languages = $language['languages'] ?? []; 
?>

@language|before = <?php
if($languages) {
	//$pagination = $this->language[$_language_idx]['pagination'];
	foreach ( $languages as $index => $language) { ?>
	
	@language [data-v-language-*]|innerText = $language['@@__data-v-language-([a-zA-Z_]+)__@@']
	
    @language [data-v-language-url] = <?php 
        echo Vvveb\url(['module' => 'language/language', 'language_id' => $language['language_id']]);
    ?>
	
	@language|after = <?php 
	} 
}
?>
