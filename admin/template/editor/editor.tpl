import(common.tpl)

//theme inputs
[data-v-theme-inputs]|deleteAllButFirstChild

[data-v-theme-inputs]|before = <?php
if(isset($this->themeComponents) && is_array($this->themeComponents)) 
{
	foreach ($this->themeComponents as $file) {?>
	
	[data-v-theme-inputs]|src = $file

	[data-v-theme-inputs]|after = <?php 
	} 
}?>


//theme components
[data-v-theme-components]|deleteAllButFirstChild

[data-v-theme-components]|before = <?php
if(isset($this->themeInputs) && is_array($this->themeInputs)) 
{
	foreach ($this->themeInputs as $file) {?>
	
	[data-v-theme-components]|src = $file

	[data-v-theme-components]|after = <?php 
	} 
}?>

//theme sections
[data-v-theme-sections]|deleteAllButFirstChild

[data-v-theme-sections]|before = <?php
if(isset($this->themeSections) && is_array($this->themeSections)) 
{
	foreach ($this->themeSections as $file) {?>
	
	[data-v-theme-sections]|src = $file

	[data-v-theme-sections]|after = <?php 
	} 
}?>


