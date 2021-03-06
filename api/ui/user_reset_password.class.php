<?php
/**
 * user_reset_password.class.php
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
 * Resets a user's password.
 * 
 * @package sabretooth\ui
 */
class user_reset_password extends base_record_action
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $subject The widget's subject.
   * @param array $args Action arguments
   * @throws exception\argument
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'user', 'reset_password', $args );
  }
  
  /**
   * Executes the action.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function execute()
  {
    $db_user = new db\user( $this->get_argument( 'id' ) );
    $ldap_manager = bus\ldap_manager::self();
    $ldap_manager->set_user_password( $db_user->name, 'password' );
  }
}
?>
