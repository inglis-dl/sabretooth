/**
 * Updates the shortcut buttons based on cookies
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */
function update_shortcuts() {
  // the home button should only be enabled if the main slot is NOT displaying the home widget
  $( "#self_shortcuts_home" ).attr( "disabled", undefined == $.cookie( "slot.main.widget" ) ||
                                                "self_home" == $.cookie( "slot.main.widget" ) )
                             .button( "refresh" );

  // the prev and next buttons should only be enabled if there are prev and next widgets available
  $( "#self_shortcuts_prev" ).attr( "disabled", undefined == $.cookie( "slot.main.prev" ) )
                             .button( "refresh" );
  $( "#self_shortcuts_next" ).attr( "disabled", undefined == $.cookie( "slot.main.next" ) )
                             .button( "refresh" );
}

/**
 * Creates a modal confirm dialog.
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @param string title The title of the dialog
 * @param string message The message to put in the dialog
 * @param callback on_confirm A function to execute if the "ok" button is pushed.
 */
function confirm_dialog( title, message, on_confirm, cancel_button ) {
  if( undefined == cancel_button ) cancel_button = true;

  $dialog = $( "#confirm_slot" );
  var buttons = new Object;
  buttons.Ok = function() {
    on_confirm();
    $dialog.dialog( "close" );
  };
  if( cancel_button ) buttons.Cancel = function() { $dialog.dialog( "close" ); };

  $dialog.html( message );
  $dialog.dialog( {
    closeOnEscape: cancel_button,
    title: title,
    modal: true,
    dialogClass: "alert",
    width: 450,
    buttons: buttons,
    open: function( event, ui ) { $( ".ui-dialog-titlebar-close", $(this).parent() ).hide(); }
  } );
}

/**
 * Creates a modal error dialog.
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @param string title The title of the dialog
 * @param string message The message to put in the dialog
 */
function error_dialog( title, message ) {
  $( "#error_slot" ).html( message );
  $( "#error_slot" ).dialog( {
    title: title,
    modal: true,
    dialogClass: "error",
    width: 450,
    open: function () {
      $(this).parents( ".ui-dialog:first" )
             .find( ".ui-dialog-titlebar" )
             .addClass( "ui-state-error" );
    },
    buttons: { Ok: function() { $(this).dialog( "close" ); } }
  } );
}

/**
 * Request information from the server.
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @param string subject The datum's subject.
 * @param string name The datum's name.
 * @param array args The arguments to pass to the operation object
 * @return mixed The requested data or null if there was an error.
 */
function get_datum( subject, name, args ) {
  if( undefined == args ) args = new Object();
  args.subject = subject;
  args.name = name;
  var request = jQuery.ajax( {
    url: "datum.php",
    async: false,
    type: "GET",
    data: jQuery.param( args ),
    complete: function( request, result ) { ajax_complete( request, 'D' ) },
    dataType: "json"
  } );
  var response = jQuery.parseJSON( request.responseText );
  return response.success ? response.data : null;
}

/**
 * Request an operation be performed to the server.
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @param string subject The action's subject.
 * @param string name The action's name.
 * @param JSON-array args The arguments to pass to the operation object
 * @return bool Whether or not the operation completed successfully
 */
function send_action( subject, name, args ) {
  if( undefined == args ) args = new Object();
  args.subject = subject;
  args.name = name;
  var request = jQuery.ajax( {
    url: "action.php",
    async: false,
    type: "POST",
    data: jQuery.param( args ),
    complete: function( request, result ) { ajax_complete( request, 'A' ) },
    dataType: "json"
  } );
  var response = jQuery.parseJSON( request.responseText );
  return response.success;
}

/**
 * Loads a url into a slot.
 * 
 * This function is used by slot_load, slot_prev, slot_next and slot_refresh.
 * It should not be used directly anywhere else.
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @param string slot The slot to place the loaded content into.
 * @param string url The url to load.
 */
function slot_url( slot, url ) {
  $.loading( {
    onAjax: true,
    mask: true,
    img: "img/loading.gif",
    delay: 300, // ms
    align: "center"
  } );
  
  $( "#" + slot + "_slot" ).html( "" );
  $( "#" + slot + "_slot" ).load( url, null,
    function( response, status, request ) { ajax_complete( request, 'W' ) }
  );
}

/**
 * Loads a widget from the server.
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @param string slot The slot to place the widget into.
 * @param string widget The widget's name (must be the name of a ui class)
 * @param string namespace The namespace to pass the args under.
 * @param JSON-array $args The arguments to pass to the widget object
 */
function slot_load( slot, widget, namespace, args ) {
  // build the url (args is an associative array)
  var query_object = new Object();
  if( undefined != args ) query_object[namespace] = args;
  query_object.slot = slot;
  query_object.widget = widget;
  var url = "widget.php?" + jQuery.param( query_object );
  slot_url( slot, url );
}

/**
 * Bring the slot back to the previous widget.
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @param string slot The slot to affect.
 */
function slot_prev( slot ) {
  var url = "widget.php?prev=1&slot=" + slot;
  slot_url( slot, url );
}

/**
 * Bring the slot to the current widget (after using slot_prev)
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @param string slot The slot to rewind.
 */
function slot_next( slot ) {
  var url = "widget.php?next=1&slot=" + slot;
  slot_url( slot, url );
}

/**
 * Reload the slot's current widget.
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @param string slot The slot to rewind.
 */
function slot_refresh( slot ) {
  args = new Object();
  args.refresh = 1;
  args.slot = slot;
  var url = "widget.php?" + jQuery.param( args );
  slot_url( slot, url );
}

/**
 * Process the request from an ajax request, displaying errors if necessary.
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @param XMLHttpRequest request The request send back from the server.
 * @param string code A code describing the type of ajax request (A for action, W for widget)
 */
function ajax_complete( request, code ) {
  if( 400 == request.status ) {
    // application is reporting an error, details are in responseText
    var response = jQuery.parseJSON( request.responseText );
    var error_code =
      code + '.' +
      ( undefined == response.error_type ? 'X' : response.error_type.substr( 0, 1 ) ) + '.' +
      ( undefined == response.error_code ? 'X' : response.error_code );

    if( 'Permission' == response.error_type ) {
      error_dialog(
        'Access Denied',
        '<p>You do not have permission to perform the selected operation.</p>' +
        '<p class="error_code">Error code: ' + error_code + '</p>' );
    }
    else if( 'Notice' == response.error_type ) {
      error_dialog(
        'Notice',
        '<p>' + response.error_message + '</p>' +
        '<p class="error_code">Error code: ' + error_code + '</p>' );
    }
    else { // any other error...
      error_dialog(
        response.error_type + ' Error',
        '<p>' +
        '  The server was unable to complete your request.<br>' +
        '  Please notify a supervisor with the error code.' +
        '</p>' +
        '<p class="error_code">Error code: ' + error_code + '</p>' );
    }
  }
  else if( 200 != request.status ) {
    // the web server has sent an error
    error_dialog(
      'Networking Error',
      '<p>' +
      '  There was an error while trying to communicate with the server.<br>' +
      '  Please notify a supervisor with the error code.' +
      '</p>' +
      '<p class="error_code">Error code: ' + code + '.200</p>' );
  }
}
