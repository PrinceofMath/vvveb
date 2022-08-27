if (Vvveb === undefined) var Vvveb = {};

jQuery(".menu-toggle").click(function() {jQuery("#container").toggleClass("small-nav"); return false;});

/*

var storage  = Storages.localStorage;
//storage.removeAll();

$('.collapse').on('hide.bs.collapse', function () {
	console.log('hide - ' + this.id);
	if (this.id) storage.remove('collapse_' + this.id);
});

$('.collapse').on('show.bs.collapse', function () {
	console.log('show - ' + this.id);
	if (this.id) storage.set('collapse_' + this.id, true);
});



$('.collapse').each(function () {
	// Default close unless saved as open
	if (storage.get('collapse_' + this.id) == true) {
		$(this).collapse('show');
	}
});
*/

//alert('asdasd');

function ucFirst(str) {
  if (!str) return str;

  return str[0].toUpperCase() + str.slice(1);
}

/*
jQuery.getJSON('https://api.creativecommons.engineering/v1/images?aspect_ratio=&categories=&extension=&format=json&license=&mature=false&page=1&page_size=20&q=mountain&size=&source=', function( data ) {
  var items = [];
  $.each( data['results'], function( key, val ) {
    items.push( "<li id='" + key + "'>" + val['title'] + "<img src=" + val['thumbnail']+ "></li>" );
  });
 
  $( "<ul/>", {
    "class": "my-new-list",
    html: items.join( "" )
  }).appendTo( "#tab-general" );
});


*/
