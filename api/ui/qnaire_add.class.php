<?php
/**
 * qnaire_add.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package sabretooth\ui
 * @filesource
 */

namespace sabretooth\ui;

/**
 * widget qnaire add
 * 
 * @package sabretooth\ui
 */
class qnaire_add extends base_view
{
  /**
   * Constructor
   * 
   * Defines all variables which need to be set for the associated template.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'qnaire', 'add', $args );
    
    // define all columns defining this record
    $this->add_item( 'name', 'string', 'Name' );
    $this->add_item( 'description', 'text', 'Description' );
  }

  /**
   * Finish setting the variables in a widget.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    parent::finish();
    
    // set the view's items
    $this->set_item( 'name', '' );
    $this->set_item( 'description', '' );

    $this->finish_setting_items();
  }
}
?>