<?php
/**
 * consent_delete.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package sabretooth\ui
 * @filesource
 */

namespace sabretooth\ui\push;
use sabretooth\log, sabretooth\util;
use sabretooth\business as bus;
use sabretooth\database as db;
use sabretooth\exception as exc;

/**
 * push: consent delete
 * 
 * @package sabretooth\ui
 */
class consent_delete extends base_delete
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'consent', $args );
  }

  /**
   * Executes the push.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    // we'll need the arguments to send to mastodon
    $args = $this->arguments;

    // replace the consent id with a unique key
    $db_consent = $this->get_record();
    unset( $args['id'] );
    $args['noid']['participant.uid'] = $db_consent->get_participant()->uid;
    $args['noid']['consent.event'] = $db_consent->event;
    $args['noid']['consent.date'] = $db_consent->date;

    parent::finish();

    // now send the same request to mastodon
    $mastodon_manager = bus\cenozo_manager::self( MASTODON_URL );
    $mastodon_manager->push( 'consent', 'delete', $args );
  }
}
?>
