@language = [data-v-component-languages] [data-v-language]
@language|deleteAllButFirstChild

[data-v-component-languages]|prepend = <?php
if (isset($_languages_idx)) $_languages_idx++; else $_languages_idx = 0;
if(isset($this->languages) && $this->languages[$_languages_idx]) {
	$languages = $this->languages[$_languages_idx];

?>


[data-v-component-languages] [data-v-language-info-*] = $languages['active']['@@__data-v-language-info-([a-zA-Z_]+)__@@']

@language|before = <?php
	
	if (is_array($languages['languages'])) {
		foreach ($languages['languages'] as $index => $language) {?>
		
		@language .dropdown-item|addClass = <?php if ($language['code'] == $languages['active']['code']) echo 'active'?>
		
		@language [data-v-language-name] = $language['name']
		@language [data-v-language-url]|href = <?php echo '//' . $language['url'];?>
		
		@language button[data-v-language-code]|value = $language['code']
		@language button[data-v-language-language_id]|value = $language['language_id']
		
		@language|after = <?php 
		} 
	}
?>

[data-v-component-languages]|append = <?php
	}
?>