
jQuery("#headerSearch").on("focus", function () {
	jQuery("#search-panel").fadeIn();
});

jQuery("#headerSearch").on("blur", function () {
	jQuery("#search-panel").fadeOut();
});


/*
if (VvvebTheme === undefined) var VvvebTheme = {};

VvvebTheme.Cart = {
	ajax: function(action, productId, quantity) {

		$.ajax({
			url: '/index.php?module=cart/cart&action=' + action,
			type: 'post',
			data: 'product_id=' + productId + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
			
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});		
		
		
		
	},
	
	add: function(productId, quantity) {
		return this.ajax('add', productId, quantity);
	},
	
	update: function(productId, quantity) {
		return this.ajax('update', productId, quantity);
	},
	
	remove: function(productId) {
		return this.ajax('remove', productId, quantity);
	}
}

VvvebTheme.Alert  = {
	
	show: function(message) {
		$('.alert-top .message').html(message);
		$('.alert-top').addClass("show").css('display', 'block');
		
		setTimeout(function () {
			$('.alert-top').fadeOut();
		}, 3000);
	}
}

$('.alert-top').on('close.bs.alert', function (e) {
    e.preventDefault();
    $(this).removeClass('show').css('display', 'none');
});

VvvebTheme.Gui = {
	
	init: function() {
		$("[data-v-vvveb-action]").each(function () {
			console.log(this);
			
			var on = "click";
			if (this.dataset.vvvebOn) on = this.dataset.vvvebOn;
			console.log(this.dataset.vVvvebAction);
			$(this).on(on, VvvebTheme.Gui[this.dataset.vVvvebAction]);
		});
	},
	
	addToCart : function () {
		
		var product = $(this).parents("[data-v-product]");
		var img = $("[data-v-img]", product).attr("src");
		var name = $("[data-v-name]", product).text();
		console.log(this);
		console.log(this.dataset.vProductId);
		VvvebTheme.Cart.add(this.dataset.vProductId);

		VvvebTheme.Alert.show('<img height=50 src="' + img + '"> &ensp; ' +  name +' was added to cart');
		
		return false;
	}
}	
		
jQuery(document).ready(function() {

	VvvebTheme.Gui.init();
});
*/