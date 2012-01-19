<?php
/**
 * source_withdraw_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package sabretooth\ui
 * @filesource
 */

namespace sabretooth\ui\widget;
use cenozo\lib, cenozo\log, sabretooth\util;

/**
 * widget source_withdraw view
 * 
 * @package sabretooth\ui
 */
class source_withdraw_view extends \cenozo\ui\widget\base_view
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
    parent::__construct( 'source_withdraw', 'view', $args );
    
    // add items to the view
    $this->add_item( 'source_id', 'enum', 'Source' );
    $this->add_item( 'sid', 'enum', 'Survey' );
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
    
    // create enum arrays
    $sources = array();
    $source_class_name = lib::get_class_name( 'database\source' );
    foreach( $source_class_name::select() as $db_source )
      $sources[$db_source->id] = $db_source->name;

    $surveys = array();
    $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'active', '=', 'Y' );
    $modifier->where( 'anonymized', '=', 'N' );
    $modifier->where( 'tokenanswerspersistence', '=', 'Y' );
    $class_name = lib::get_class_name( 'database\limesurvey\surveys' );
    foreach( $class_name::select( $modifier ) as $db_survey )
      $surveys[$db_survey->sid] = $db_survey->get_title();

    // set the view's items
    $this->set_item( 'source_id', $this->get_record()->source_id, true, $sources );
    $this->set_item( 'sid', $this->get_record()->sid, true, $surveys );

    $this->finish_setting_items();
  }
}
?>