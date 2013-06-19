
<form action="insert.php" method="post" id="eanident">
<div id="wrap" class="form">
	<div id="input">EAN <input name="ean" class="ident"></div> 
	<div id="input">IDENT <input name="pid" class="ident"></div>
</div>
<div id="wrap"><input type="Submit" style="visibility: hidden;"></div>

</form>

<div id="wrap">
	<div id="result">RES</div>
</div>
</script>

<script type="text/javascript">
/* 
	Tapstop bei Enter
	Wenn Return gedrückt wird, wird in das nächste (input)feld gesprungen
	http://stackoverflow.com/questions/4649604/jquery-select-next-text-field-on-enter-key-press 
*/
$(document).ready(function(){
$(".ident").keypress(function(event) {
        if(event.keyCode == 13) { 
        textboxes = $("input.ident");
        debugger;
        currentBoxNumber = textboxes.index(this);
        if (textboxes[currentBoxNumber + 1] != null) {
            nextBox = textboxes[currentBoxNumber + 1]
            nextBox.focus();
            nextBox.select();
            event.preventDefault();
            return false 
            }
        }
    });
})

/* ********
	Senden Via JQuery 
 * */
$("#eanident").submit(function(event) {
 
  /* stop form from submitting normally */
  event.preventDefault();
 
  /* get some values from elements on the page: */
  var $form = $( this ),
      term = $form.find( 'input[name="ean"]' ).val(),
      term2 = $form.find( 'input[name="pid"]' ).val(),
      url = $form.attr( 'action' );
 
  /* Send the data using post */
  var posting = $.post( url, { ean: term, pid: term2 } );
 
  /* Put the results in a div */
  posting.done(function( data ) {
    var content = $( data );
    $( "#result" ).empty().append( content );
    $form.find( 'input[name="ean"]' ).val(''); /* Input Leeren */
    $form.find( 'input[name="pid"]' ).val(''); /* Input Leeren */
    $form.find( 'input[name="ean"]' ).select(); /* Selectiere zu Ean */
  });
});
</script>