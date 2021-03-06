<?php
/**
 * datum.php
 * 
 * Web script which can be called to retrieve data from the system.
 * This script returns data in JSON format.
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @throws exception\argument, exception\runtime
 */
namespace sabretooth;
ob_start();
 
// the array to return, encoded as JSON
$result_array = array( 'success' => true );

try
{
  // load web-script common code
  require_once 'sabretooth.inc.php';
  
  // Try creating an operation and calling the datum provided by the post.
  if( !isset( $_GET['subject'] ) ) throw new exception\argument( 'subject', NULL, 'DATUM__SCRIPT' );
  if( !isset( $_GET['name'] ) ) throw new exception\argument( 'name', NULL, 'DATUM__SCRIPT' );

  $datum_name = $_GET['subject'].'_'.$_GET['name'];
  $datum_class = 'sabretooth\\ui\\'.$datum_name;
  $datum_args = isset( $_GET ) ? $_GET : NULL;

  // create the operation using the provided args then execute it
  $operation = new $datum_class( $datum_args );
  if( !is_subclass_of( $operation, 'sabretooth\\ui\\datum' ) )
    throw new exception\runtime(
      'Invoked operation "'.$datum_class.'" is invalid.', 'DATUM__SCRIPT' );
  
  $result_array['data'] = $operation->get_data();
  business\session::self()->log_activity( $operation, $datum_args );
  log::notice(
    sprintf( 'finished script: executed datum "%s", processing time %0.2f seconds',
             $datum_class,
             util::get_elapsed_time() ) );
}
catch( exception\base_exception $e )
{
  $type = $e->get_type();
  $result_array['success'] = false;
  $result_array['error_type'] = ucfirst( $type );
  $result_array['error_code'] = $e->get_code();
  $result_array['error_message'] = 'notice' == $type ? $e->get_notice() : '';

  if( 'notice' != $type ) log::err( ucwords( $type )." ".$e );
}
catch( \Exception $e )
{
  $code = util::convert_number_to_code( SYSTEM_BASE_ERROR_NUMBER );
  log::err( "Last minute ".$e );
  $result_array['success'] = false;
  $result_array['error_type'] = 'System';
  $result_array['error_code'] = $code;
  $result_array['error_message'] = '';

  if( class_exists( 'sabretooth\log' ) ) log::err( "Last minute ".$e );
}

// flush any output
ob_end_clean();

if( true == $result_array['success'] )
{
  print json_encode( $result_array );
}
else
{
  util::send_http_error( json_encode( $result_array ) );
}
?>
