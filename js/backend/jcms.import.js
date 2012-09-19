$(function() {
  $('#import_all').click(function(e){
	  e.preventDefault();
	  $('form[name=import_files] input[type=checkbox]').attr('checked','checked');
  });
});