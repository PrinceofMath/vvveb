# Components

## Overview

Component object is used to define the html blocks that can be added and edited on the page.

To add a component you need to use `Vvveb.Components.add` method or extend an existing component, like a base component with `Vvveb.Components.extend`

For example image component extends _base component and is defined as

```js
Vvveb.Components.extend("_base", "html/image", {
    nodes: ["img"],
    name: "Image",
    html: '<img src="../libs/builder/icons/image.svg" height="128" width="128">',
    image: "icons/image.svg",
    properties: [{
        name: "Image",
        key: "src",
        htmlAttr: "src",
        inputtype: FileUploadInput
    }, {
        name: "Width",
        key: "width",
        htmlAttr: "width",
        inputtype: TextInput
    }, {
        name: "Height",
        key: "height",
        htmlAttr: "height",
        inputtype: TextInput
    }, {
        name: "Alt",
        key: "alt",
        htmlAttr: "alt",
        inputtype: TextInput
    }]
});
```


Base component does not extend any existing component and is using  `Vvveb.Components.add` being defined as 

```js
Vvveb.Components.add("_base", {
    name: "Element",
	properties: [{
        name: "Id",
        key: "id",
        htmlAttr: "id",
        inputtype: TextInput
    }, {
        name: "Class",
        key: "class",
        htmlAttr: "class",
        inputtype: TextInput
    }]
});
```    

In this way any component that extends *_base* component inherits two properties id and class used to edit the corresponding html attributes.

*Note*: _base component is loaded for any html node that has no specific component assigned, for example when clicking on a regular *span* node.

## Component definition

Component object can have the following keys

* **name** - Used to display the name on the components list in the left panel.
* **image** - Image url to display in the components list.
* **html** - When dropping the component on the page this html is used to insert it in the page.
* **nodes** - An array with dom node names to allow the editor to detect the Component when clicking on the corresponding node, example `nodes: ["h1", "h2","h3", "h4","h5","h6"],` or `nodes: ["img"],`
* **classes** - An array with class names to allow the editor to detect the Component when clicking on the corresponding node, example `classes: ["btn", "btn-link"]`
* **classesRegex** - An array with regexs used for class names to allow the editor to detect the Component when clicking on the corresponding node, example `classesRegex: ["col-"],` this regex is used by Grid Component to detect bootstrap columns such as col-md-3 or col-sm-6.
* **attributes** - An array with attributes  names to allow the editor to detect the Component when clicking on the corresponding node, example `attributes: ["data-component-maps"],` this attribute is used by maps component.
* **afterDrop** -  *function (node) {}*  A function that is called after the element is droped, the node of the new element is passed as parameter, for example map component uses this function to change the html of the node fom a image of a map with the actual map iframe for performance reasons when dragging.
* **init** - *function (node) {}* A function that is called after the Component properties are loaded and displayed in the right panel , the node of the new element is passed as parameter, for example video component that supports youtube, vimeo and html5 uses this event to hide vimeo and html5 inputs when a youtube video is detected.
* **beforeInit** - *function (node) {}* A function that is called before Component properties are displayed , the node of the new element is passed as parameter, this can be useful to dinamically change component properties for example Select Input or Grid component that needs to load a variable number of inputs based on child nodes uses this event to alter component properties and add the necessary properties for each child node.
* **onChange** - *function (node, property, value)* This event is called when a component input in the right panel is changed, the first parameter is the dom *node* that is edited, second parameter *property* is the name of the property that has changed (the *key* attribute of the property), *value* is the new value of the property.
Usually each input has it's own onChange event but because the component onChange event is triggered for all inputs can be usefull when using a common function to process all input changes.
This event is usefull when changing complex features, for example the map component uses it to compose a new google maps url by adding all component properties such as latitude, longitude and zoom etc.
* **properties** - A collection of inputs and corespponding configuration for each, to allow the Component to be edited.

A complete Component definition with all possible keys

```js
Vvveb.Components.extend("_base", "html/image", {
	name: "Image",
	image: "icons/image.svg",
	html: '<img src="../libs/builder/icons/image.svg" height="128" width="128">',
	nodes: ["img"],
	classes: ["btn", "btn-link"],
	attributes: ["data-component-maps"],
	afterDrop: function (node) {},
	init: function (node) {},
	beforeInit: function (node) {},
	onChange: function (node, property, value) {},
	properties: [{
		name: "Id",
		key: "id",
		htmlAttr: "id",
		inputtype: TextInput
    }
});   
```
## Component properties

Component properties are used to define the inputs that are used to edit the component. 

```js
properties: [{
        name: "Id",
        key: "id",
        htmlAttr: "id",
        inputtype: TextInput
    }, {
        name: "Class",
        key: "class",
        htmlAttr: "class",
        inputtype: TextInput
    }]
```    

A property can have the following keys

* **name** - Used as label for the input.
* **key** - Unique identifier for the input, this is pased on component *onChange* event second parameter *property*.
* **inputtype** - The input type used for the property, any Input object such as TextInput, SelectInput, ToggleInput, ColorInput etc from *inputs.js* can be used.
* **htmlAttr** - If htmlAttr is specified then the value is used to edit the coresponding html attribute, the input is filled automatically with the initial value of the attribute and updated automatically when the input is changed, no need to use onChange event, for example *htmlAttr: "id",*
	* **style** - If htmlAttr is set to *style* then the property key is used as the css property to be edited, in the example below *background-color* css property is edited directly using ColorInput that will display a color picker.
	* **class** - If htmlAttr is set to *class* and the property has *validValues* key then all classed that are contained in validValues array are removed before adding the new value, this can be usefull when having a select input to choose a unique class from a set.
	For example in the example below bootstrap container component uses validValues to allow either *container* or *container-fluid* class to be added from a select.
```js
properties: [
{
	name: "Background Color",
	key: "background-color",
	htmlAttr: "style",
	inputtype: ColorInput,
},
{
	name: "Type",
	key: "type",
	htmlAttr: "class",
	inputtype: SelectInput,
	validValues: ["container", "container-fluid"],
	data: {
		options: [{
			value: "container",
			text: "Default"
		}, {
			value: "container-fluid",
			text: "Fluid"
		}]
	}        name: "Class",
	key: "class",
	htmlAttr: "class",
	inputtype: TextInput
}]
```    
* **onChange** - *function(node, value)* This event is triggered when the input is changed (this can depend on Input definition, check inputs.js) you can use this when htmlAttr or style is not enough.
* **init** - *function(node)* This function is called when the input is initialized, this can be used for example to load the input with a different value than default.
For example when editing width css property and using a NumberInput you need to use init to remove the px from "100px" to set the value for NumberInput correctly.
```js
{
	name: "Width",
	key: "width",
	htmlAttr: "style",
	parent:"",
	inputtype: NumberInput,//can also be replaced with RangeInput
	data: {
		value: "320",//default
		min: "50",
		max: "1024",
		step: "10"
	},
	init: function (node)//use init because number input does not ignore "px"
	{
		return parseInt($(node).css("width"));//remove px
	}
}
```	
* **data** - Some Inputs need aditional data, for example ToggleInput needs values for *on* and *off* states, or SelectInput needs *data:{options: [{}]}* for values 
For example using toggle to set disabled to true or false for a bootstrap button
```js    
//ToggleInput
{
	name: "Disabled",
	key: "disabled",
	htmlAttr: "disabled",
	inputtype: ToggleInput,
	data: {
		on: "disabled-true",
		off: "disabled-false"
	}
}
//SelectInput
{
        name: "Size",
        key: "size",
        htmlAttr: "class",
        inputtype: SelectInput,
        validValues: ["btn-lg", "btn-sm"],
        data: {
            options: [{
                value: "",
                text: "Default"
            }, {
                value: "btn-lg",
                text: "Large"
            }, {
                value: "btn-sm",
                text: "Small"
            }]
        }
    }
```    
* **child** - Sometimes you need to edit a node that is inside the parent node, for this you can use *child* property the value must be the css selector for the child element.
For example bootstrap progress bar component has two divs, one for background and one for the progress to use a input to change the progress div instead of the background div, child is set as *child:".progress-bar"*.
```js    
{
	name: "Animated",
	key: "animated",
	child:".progress-bar",
	htmlAttr: "class",
	validValues: ["", "progress-bar-animated"],
	inputtype: ToggleInput,
	data: {
		on: "progress-bar-animated",
		off: "",
	}
}	
```
* **parent** - Same as child but used to edit parent elements, when only parent is set with no selector then the parentNode is used, if a css selector is used then the selector is used to find parent node (this uses jQuery.parents())


## Component group

For a component to be visible in the left panel and to be used for drag and drop it needs to be added to a component group, you can do this by including it in the `Vvveb.ComponentsGroup` array using the group name as a key.

For example Widgets component group has the following definition.

```js
Vvveb.ComponentsGroup['Widgets'] = ["widgets/googlemaps", "widgets/video"];
```

[Components]: <Components>
[Component]: <Components>
[Input]: <Inputs>
[Inputs]: <Inputs>
