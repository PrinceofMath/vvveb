# Inputs

## Overview

Inputs objects are used to edit component properties

For example TextInput extends Input object and is defined as 

```js
var TextInput = $.extend({}, Input, {

    events: {
        "keyup": ['onChange', 'input'],
	 },

	setValue: function(value) {
		$('input', this.element).val(value);
	},
	
	init: function(data) {
		return this.render("textinput", data);
	},
  }
);
```


Inputs also require a template that is defined as a <script> tag in the editor html (inside editor.html) with the id `vvveb-input-inputname` for example for text input is `vvveb-input-textinput` and is defined as

```js
<script id="vvveb-input-textinput" type="text/html">
	
	<div>
		<input name="{%=key%}" type="text" class="form-control"/>
	</div>
	
</script>
```

{%=key%} is used to set the unique input name defined as "key" in component properties, if additional data is provided then these must be set in the template, for example ToggleInput needs *on* and *off*

```js
<script id="vvveb-input-toggle" type="text/html">
	
    <div class="toggle">
        <input type="checkbox" name="{%=key%}" value="{%=on%}" data-value-off="{%=off%}" data-value-on="{%=on%}" class="toggle-checkbox" id="{%=key%}">
...
```        



## Definition

Usually Input objects define the following keys

* *events* - This provides a collection of definitions for functions to be called on certain events, the format is *event_name :['InputObject_function_to_be_called', 'css_selector_for_element_in_template']*
For example text input monitors keyup event for input element in the template, while select input monitors onchange event for select element in template, by default they both call onChange function on parent Input object.

```js
var TextInput = $.extend({}, Input, {

    events: {
        "keyup": ['onChange', 'input'],
	 },
...


var SelectInput = $.extend({}, Input, {
	

    events: {
        "change": ['onChange', 'select'],
	 },
...
```	
Sometimes you need to read another attribute than value from the input such as checked for checkbox in this case you need to override onChange method to use *checked* attribute.

```js
var CheckboxInput = $.extend({}, Input, {

	onChange: function(event, node) {
		
		if (event.data && event.data.element)
		{
			event.data.element.trigger('propertyChange', [this.checked, this]);
		}
	},
```

* *setValue* - This method is called automatically by Vvveb.Builder when setting the value for the input.
* *init* - This method is called when the input is initialized, the return value is used as html to render the input, by default the parent Input.render() is called with the template name and additional data as parameters.  


## Parent Input

All input object extends Input object that handles template loading and processing and also provides a default onChange function that automatically processes events using value attribute.

```js
var Input = {
	
	init: function(name) {
	},


	onChange: function(event, node) {
		
		if (event.data && event.data.element)
		{
			event.data.element.trigger('propertyChange', [this.value, this]);
		}
	},

	renderTemplate: function(name, data) {
		return tmpl("vvveb-input-" + name, data);
	},

	render: function(name, data) {
		this.element = $(this.renderTemplate(name, data));
		
		//bind events
		if (this.events)
		for (var event in this.events)
		{
			fun = this[ this.events[event][0] ];
			el = this.events[event][1];
		
			this.element.on(event, el, {element: this.element}, fun);
		}
		
		return this.element;
	}
};
```

You can check editor.html for input templates and inputs.js for inputs code.
