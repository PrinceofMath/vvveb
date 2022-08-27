import(common.tpl)
@theme = [data-v-themes] [data-v-theme]

@theme|deleteAllButFirstChild
[data-v-themes]  [data-v-theme]|before = <?php
if(isset($this->themes) && is_array($this->themes)) {
	foreach ($this->themes as $index => $theme) { ?>
	
    @theme [data-v-theme-*]|innerText  = $theme['@@__data-v-theme-([-_\w]+)__@@']
    @theme a[data-v-theme-*]|href  = $theme['@@__data-v-theme-([-_\w]+)__@@']
    @theme img[data-v-theme-*]|src  = $theme['@@__data-v-theme-([-_\w]+)__@@']
    
	[data-v-themes]  [data-v-theme]|after = <?php 
	} 
}?>
