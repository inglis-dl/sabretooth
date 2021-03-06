<?php
/**
 * consent_list.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package sabretooth\ui
 * @filesource
 */

namespace sabretooth\ui;
use sabretooth\log, sabretooth\util;
use sabretooth\business as bus;
use sabretooth\database as db;
use sabretooth\exception as exc;

/**
 * widget consent list
 * 
 * @package sabretooth\ui
 */
class consent_list extends base_list_widget
{
  /**
   * Constructor
   * 
   * Defines all variables required by the consent list.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'consent', $args );
    
    $this->add_column( 'event', 'string', 'Event', true );
    $this->add_column( 'date', 'datetime', 'Date', true );
  }
  
  /**
   * Set the rows array needed by the template.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    parent::finish();

    foreach( $this->get_record_list() as $record )
    {
      $this->add_row( $record->id,
        array( 'event' => $record->event,
               'date' => $record->date ) );
    }

    $this->finish_setting_rows();
  }
}
?>
