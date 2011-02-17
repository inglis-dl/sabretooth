<?php
/**
 * site_add_user.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package sabretooth\ui
 * @filesource
 */

namespace sabretooth\ui;

/**
 * widget site add_user
 * 
 * @package sabretooth\ui
 */
class site_add_user extends base_add_list
{
  /**
   * Constructor
   * 
   * Defines all variables which need to be set for the associated template.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $name The name of the operation.
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'site', 'user', $args );

    // build the role list widget
    $this->role_list = new role_list( $args );
    $this->role_list->set_parent( $this, 'edit' );
    $this->role_list->set_heading( 'Select roles to grant to the selected users' );
  }

  public function finish()
  {
    parent::finish();

    $this->role_list->finish();
    $this->set_variable( 'role_list', $this->role_list->get_variables() );
  }
}
?>
