<?php
/**
 * user_new_access.class.php
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
 * action user new_access
 * 
 * @package sabretooth\ui
 */
class user_new_access extends base_new_record
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Action arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'user', 'access', $args );
  }

  /**
   * Executes the action.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function execute()
  {
    foreach( $this->get_argument( 'role_id_list' ) as $role_id )
    {
      $this->get_record()->add_access( $this->get_argument( 'site_id_list' ), $role_id );
    }
  }
}
?>
