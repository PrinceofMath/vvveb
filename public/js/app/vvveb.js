if (VvvebTheme === undefined) var VvvebTheme = {};

VvvebTheme.Ajax = {
	call: function(parameters, element, callback) {

		$.ajax({
			url: '/index.php?module=' +  parameters["module"] + '&_component_ajax=' + parameters["component"] + '&_component_id=0&action=' + parameters["action"],
			type: 'post',
			data: parameters,
			//dataType: 'json',
			beforeSend: function() {
				$('.loading', element).removeClass('d-none');
				$('.button-text', element).addClass('d-none');
				if ($(element).is('button'))  {
					$(element).attr("disabled", "true");
				}
			},
			complete: function() {
				$('.loading', element).addClass('d-none');
				$('.button-text', element).removeClass('d-none');
				if ($(element).is('button')) {
					$(element).removeAttr("disabled");
				}
				//$('#cart > button').button('reset');
			},
			success: function(data) {
				//$("header [data-v-component-cart]")[0].outerHTML = data;
				if (callback) callback(data);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});		
		
	}
}

VvvebTheme.Cart = {
	
	module: 'cart/cart',
	component: 'cart',
	component_id: '0',
	
	ajax: function(action, parameters, element, callback) {
		parameters['module'] = this.module;
		parameters['action'] = action;
		parameters['component'] = this.component;
		VvvebTheme.Ajax.call(parameters, element, callback);
	},
	
	add: function(productId, quantity, element, callback) {
		return this.ajax('add',{'product_id':productId, 'quantity':quantity}, element, function (data) {
			$("header [data-v-component-cart]")[0].outerHTML = data;
			if (callback) {
				callback();
			}
		});
	},
	
	update: function(productId, quantity) {
		return this.ajax('update',{'product_id':productId, 'quantity':quantity});
	},
	
	remove: function(productId, element, callback) {
		return this.ajax('remove', {'product_id':productId}, callback);
	}
}
VvvebTheme.Comments = {
	
	module: 'content/post',
	
	ajax: function(action, parameters, element, callback) {
		parameters['module'] = this.module;
		parameters['action'] = action;
		VvvebTheme.Ajax.call(parameters, element, callback);
	},
	
	add: function(parameters, element, callback) {
		return this.ajax('addComment',parameters, element, callback);
	},
	
	update: function(productId, quantity) {
		return this.ajax('update',{'product_id':productId, 'quantity':quantity});
	},
	
	remove: function(productId) {
		return this.ajax('remove', {'product_id':productId});
	}
}

VvvebTheme.User = {
	
	module: 'user/login',
	component: 'user',
	component_id: '0',
	
	ajax: function(action, parameters, element, callback) {
		parameters['module'] = this.module;
		parameters['action'] = action;
		parameters['component'] = this.component;
		VvvebTheme.Ajax.call(parameters, element, callback);
	},
	
	login: function(parameters, element, callback) {
		return this.ajax('index' ,parameters, element, callback);
	},
}

VvvebTheme.Search = {
	
	module: 'search',
	component: 'search',
	component_id: '0',
	
	ajax: function(action, parameters, element, callback) {
		parameters['module'] = this.module;
		parameters['action'] = action;
		parameters['component'] = this.component;
		VvvebTheme.Ajax.call(parameters, element, callback);
	},
	
	query: function(parameters, element, callback) {
		return this.ajax('index' ,parameters, element, callback);
	},
}

VvvebTheme.Alert  = {
	
	show: function(message) {
		$('.alert-top .message').html(message);
		$('.alert-top').addClass("show").css('display', 'block');
		
		setTimeout(function () {
			$('.alert-top').fadeOut();
		}, 4000);
	}
}

$('.alert-top').on('close.bs.alert', function (e) {
    e.preventDefault();
    $(this).removeClass('show').css('display', 'none');
});

VvvebTheme.Gui = {
	
	init: function() {
		var events = [];
		
		$("[data-v-vvveb-action]").each(function () {

			var on = "click";
			if (this.dataset.vVvvebOn) on = this.dataset.vVvvebOn;
			var event = '[data-v-vvveb-action="' + this.dataset.vVvvebAction + '"]';

			if (events.indexOf(event) > -1) return;
			events.push(event);

			$(document).on(on, event, VvvebTheme.Gui[this.dataset.vVvvebAction]);
		});
		/*
		for (actionName in VvvebTheme.Gui)
		{
			if (actionName == "init") continue;
			//console.log(actionName);
			$(document).on("click", '[data-v-vvveb-action="' + actionName + '"]', VvvebTheme.Gui[actionName]);
		}*/
	},
	
	addToCart : function (e) {
		var product = $(this).parents("[data-v-product],[data-v-component-product]");
		var img = $("[data-v-product-img],[data-v-product-image]", product).attr("src");
		var name = $("[data-v-product-name]", product).text();
		var id = this.dataset.product_id;

		if (!id) {
			id = product[0].dataset.product_id;
			if (!id) {
				id = $('input[name="product_id"]', product).val();
			}
		}

		VvvebTheme.Cart.add(id, 1, this, function() {
			VvvebTheme.Alert.show('<img height=50 src="' + img + '"> &ensp; ' +  name +' was added to cart');
		});
		
		return false;
	},	
	
	removeFromCart : function (e) {
		
		var product = $(this).parents("[data-v-product],[data-v-component-product], [data-v-cart-product]");
		var img = $("[data-v-product-img],[data-v-product-image], [data-v-cart-product-img]", product).attr("src");
		var name = $("[data-v-product-name]", product).text();
		var id = this.dataset.product_id;

		if (!id) {
			id = product[0].dataset.product_id;
			if (!id) {
				id = $('input[name="product_id"]', product).val();
			}
		}
		VvvebTheme.Cart.remove(id, this, function() {
			VvvebTheme.Alert.show('<img height=50 src="' + img + '"> &ensp; ' +  name +' was removed from cart');
			product.remove();
		});
		
		return false;
	},
	
	addComment : function (e) {
		
		var parameters = $(this).serializeArray();
		
		VvvebTheme.Comments.add(parameters, this, function() { 
				alert("Comment added");
		});
		e.preventDefault();
		
	},	
	
	search : function (e) {
		
		var parameters = $(this).serializeArray();
		
		VvvebTheme.Search.query(parameters, this, function(data) { 
				$("[data-v-component-search]")[0].outerHTML = data;
		});
		e.preventDefault();
		
	},
	
	login : function (e) {
		
		var parameters = $(this).serializeArray();
		
		VvvebTheme.User.login(parameters, this, function(data) { 
			$("[data-v-component-user]")[0].outerHTML = data;
			//	alert("Login");
		});
		e.preventDefault();
		
	}
}	
		
jQuery(document).ready(function() {
	VvvebTheme.Gui.init();
});
