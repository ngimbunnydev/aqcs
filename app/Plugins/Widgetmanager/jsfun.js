function openWidgetPanel(){

	$('#widget_center').modal();
	/*$("#widget_center").modal("show").on('shown.bs.modal', function () {
    	
    });*/
	
}//end


$(".btn-widget").click(function(e){
	var widgetStr=$(this).text();
	editorContext.invoke('editor.insertText', widgetStr);
	$('#widget_center').modal('toggle');
});