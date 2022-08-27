## Overview

The editor is build using Bootstrap 4 and jQuery.

The editor html is located in editor.html and to load the demo editor the following scripts are included.


```html
<!-- jquery-->
<script src="js/jquery.min.js"></script>
<!-- Enable shortcut support such as ctrl+z for undo and ctrl+e for export etc-->
<script src="js/jquery.hotkeys.js"></script>


<!-- bootstrap-->
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<!-- builder code-->
<!-- This is the main editor code -->
<script src="libs/builder/builder.js"></script>

<!-- undo manager-->
<script src="libs/builder/undo.js"></script>

<!-- inputs-->
<!-- The inputs library, here is the code for inputs such as text, select etc used for component properties -->
<script src="libs/builder/inputs.js"></script>

<!-- components-->
<!-- Components for Bootstrap 4 group -->
<script src="libs/builder/components-bootstrap4.js"></script>
<!-- Components for Widgets group -->
<script src="libs/builder/components-widgets.js"></script>


<!-- plugins -->

<!-- code mirror libraries - code editor syntax highlighting for html editor -->
<link href="libs/codemirror/lib/codemirror.css" rel="stylesheet"/>
<link href="libs/codemirror/theme/material.css" rel="stylesheet"/>
<script src="libs/codemirror/lib/codemirror.js"></script>
<script src="libs/codemirror/lib/xml.js"></script>
<script src="libs/codemirror/lib/formatting.js"></script>

<!-- code mirror vvveb plugin -->
<!-- replaces default textarea as html code editor with codemirror-->
<script src="libs/builder/plugin-codemirror.js"></script>	

```
To initialize the editor Vvveb.Builder.init is called.

The first parameter is the url to load for editing, this must be on the same subdomain to allow editing.

The second parameter is a function to call when the page is finished loading, by default the editor Gui.init() is called.

```javascript
$(document).ready(function() 
{
	Vvveb.Builder.init('demo/index.html', function() {
		//load code after page is loaded here
		Vvveb.Gui.init();
	});
});
</script>
```

## Structure

[[img/structure.png]]

`Component Group` is a collection of [Components], for example Bootstrap 4 group is composed of Components such as `Button` and `Grid`, this object is used only for grouping components in the editor left panel.

For example Widgets component group has only two components video and maps and is defined as

```javascript
Vvveb.ComponentsGroup['Widgets'] = ["widgets/googlemaps", "widgets/video"];
```

A [Component] is an object that provides html that can be dropped on the canvas and also properties that can be edited when the component is selected, for example Video Component.

The html link [Component] that has Url and Target properties is defined as 

```javascript
Vvveb.Components.extend("_base", "html/link", {
    nodes: ["a"],
    name: "Link",
    properties: [{
        name: "Url",
        key: "href",
        htmlAttr: "href",
        inputtype: LinkInput
    }, {
        name: "Target",
        key: "target",
        htmlAttr: "target",
        inputtype: TextInput
    }]
});
```


An [Input] object is used in [Component] properties collection for editing the property, for example text input, select, color, grid row etc
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

[Components]: <Components>
[Component]: <Components>
[Input]: <Inputs>
[Inputs]: <Inputs>
