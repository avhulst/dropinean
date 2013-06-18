
<form action="insert.php" method="post" id="eanident">
<div id="wrap" class="form">
	<div id="input">EAN <input name="ean" class="ident"></div> 
	<div id="input">IDENT <input name="pid"></div>
</div>
<div id="wrap"><input type="Submit"></div>

</form>

<div id="wrap">
	<div id="result">RES</div>
</div>
</script>

<script type="text/javascript">
$(document).ready(function(){
$(".ean2ident").keypress(function(event) {
        if(event.keyCode == 13) { 
        textboxes = $("input.ean2ident");
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
  var posting = $.post( url, { ean: term, pid: term2 } );
 
  /* Put the results in a div */
  posting.done(function( data ) {
    var content = $( data );
    $( "#result" ).empty().append( content );
  });
});
</script>