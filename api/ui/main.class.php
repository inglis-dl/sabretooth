<?php
/**
 * main.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package sabretooth\ui
 * @filesource
 */

namespace sabretooth\ui;
use cenozo\lib, cenozo\log, sabretooth\util;

/**
 * Class that manages variables in main user interface template.
 * 
 * @package sabretooth\ui
 */
class main extends \cenozo\ui\main
{
  /**
   * Constructor
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public static function get_variables()
  {
    $session = lib::create( 'business\session' );
    $survey_manager = lib::create( 'business\survey_manager' );
    $variables = parent::get_variables();
    $variables['survey_url'] = $survey_manager->get_survey_url();
    $variables['show_menu'] = false == $variables['survey_url'] &&
                              ( 'operator' != $session->get_role()->name ||
                                is_null( $session->get_current_assignment() ) );
    return $variables;
  }
}
?>
