<?php
/**
 * tokens.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace sabretooth\database\limesurvey;
use cenozo\lib, cenozo\log, sabretooth\util;

/**
 * Access to limesurvey's tokens_SID tables.
 */
class tokens extends sid_record
{
  /**
   * Updates the token attributes with current values from Mastodon
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\participant $db_participant The record of the participant linked to this token.
   * @param boolean $extended Whether or not to included extended parameters.
   * @access public
   */
  public function update_attributes( $db_participant )
  {
    if( NULL == $this->token )
    {
      log::warning( 'Tried to update attributes of token without token string.' );
      return;
    }

    $mastodon_manager = lib::create( 'business\cenozo_manager', MASTODON_URL );
    $db_user = lib::create( 'business\session' )->get_user();

    // determine the first part of the token
    $token_part = substr( $this->token, 0, -1 );
    
    // fill in the email
    $this->email = $db_participant->email;
    
    // determine the attributes from the survey with the same ID
    $db_surveys = lib::create( 'database\limesurvey\surveys', static::get_sid() );

    foreach( explode( "\n", $db_surveys->attributedescriptions ) as $attribute )
    {
      if( 10 < strlen( $attribute ) )
      {
        $key = 'attribute_'.substr( $attribute, 10, strpos( $attribute, '=' ) - 10 );
        $value = substr( $attribute, strpos( $attribute, '=' ) + 1 );
        $matches = array(); // for pregs below
        
        // now get the info based on the attribute name
        if( false !== strpos( $value, 'address' ) )
        {
          $db_address = $db_participant->get_primary_address();
          
          if( 'address street' == $value )
          {
            if( $db_address )
            {
              $this->$key = $db_address->address1;
              if( !is_null( $db_address->address2 ) ) $this->$key .= ' '.$db_address->address2;
            }
            else
            {
              $this->$key = '';
            }
          }
          else if( 'address city' == $value )
          {
            $this->$key = $db_address ? $db_address->city : '';
          }
          else if( 'address province' == $value )
          {
            $this->$key = $db_address ? $db_address->get_region()->name : '';
          }
          else if( 'address postal code' == $value )
          {
            $this->$key = $db_address ? $db_address->postcode : '';
          }
        }
        else if( 'age' == $value )
        {
          $this->$key = strlen( $db_participant->date_of_birth )
                      ? util::get_interval(
                          util::get_datetime_object( $db_participant->date_of_birth ) )->y
                      : "";
        }
        else if( 'written consent received' == $value )
        {
          $consent_mod = lib::create( 'database\modifier' );
          $consent_mod->where( 'event', 'like', 'written %' );
          $this->$key = 0 < $db_participant->get_consent_count( $consent_mod ) ? '1' : '0';
        }
        else if( false !== strpos( $value, 'HIN' ) )
        {
          // get HIN info from mastodon (fake it if mastodon is not enabled)
          $participant_info = array( 'hin_access' => '', 'hin_missing' => 1 );
          if( $mastodon_manager->is_enabled() )
            $participant_info =
              $mastodon_manager->pull(
                'participant', 'primary', array( 'uid' => $db_participant->uid ) );
          
          if( 'consented to provide HIN' == $value )
          {
            $this->$key = $participant_info->data->hin_access;
          }
          else if( 'HIN recorded' == $value )
          {
            $this->$key = $participant_info->data->hin_missing ? 0 : 1;
          }
        }
        else if( 'INT_13a' == $value || 'INCL_2f' == $value )
        {
          // TODO: This is a custom token attribute which refers to a specific question in the
          // introduction survey.  This code is not generic and needs to eventually be made
          // generic.
          $survey_class_name = lib::get_class_name( 'database\limesurvey\survey' );
          $source_survey_class_name = lib::get_class_name( 'database\source_survey' );
          
          $db_interview = lib::create( 'business\session')->get_current_assignment()->get_interview();
          $phase_mod = lib::create( 'database\modifier' );
          $phase_mod->where( 'rank', '=', 1 );
          $phase_list = $db_interview->get_qnaire()->get_phase_list( $phase_mod );

          // determine the SID of the first phase of the questionnaire (where the question is asked)
          if( 1 == count( $phase_list ) )
          {
            $db_phase = current( $phase_list );
            $db_source_survey = $source_survey_class_name::get_unique_record(
              array( 'phase_id', 'source_id' ),
              array( $db_phase->id, $db_participant->source_id ) );
            $survey_class_name::set_sid(
              is_null( $db_source_survey ) ? $db_phase->sid : $db_source_survey->sid );

            $survey_mod = lib::create( 'database\modifier' );
            $survey_mod->where( 'token', 'LIKE', $token_part.'%' );
            $survey_mod->order_desc( 'datestamp' );
            $survey_list = $survey_class_name::select( $survey_mod );

            $found = false;
            foreach( $survey_list as $db_survey )
            { // loop through all surveys until an answer is found
              try
              {
                $this->$key = $db_survey->get_response( $value );
                // INT_13a matches any survey response, others match any NON NULL response
                if( 'INT_13a' == $value || !is_null( $this->$key ) ) $found = true;
              }
              catch( \cenozo\exception\runtime $e )
              {
                // ignore the error and continue without setting the attribute
              }
              
              if( $found ) break;
            }
          }
        }
        else if( 'operator first_name' == $value )
        {
          $this->$key = $db_user->first_name;
        }
        else if( 'operator last_name' == $value )
        {
          $this->$key = $db_user->last_name;
        }
        else if( 'participant_source' == $value )
        {
          $db_source = $db_participant->get_source();
          $this->$key = is_null( $db_source ) ? '(none)' : $db_source->name;
        }
        else if( 'previous CCHS contact date' == $value )
        {
          $event_class_name = lib::get_class_name( 'database\event' );
          $datetime_list = $db_participant->get_event_datetime_list(
            $event_class_name::get_unique_record( 'name', 'completed pilot interview' ) );
          $this->$key = 0 < count( $datetime_list ) ? current( $datetime_list ) : NULL;
        }
        else if( false !== strpos( $value, 'alternate' ) )
        {
          $alternate_list = $db_participant->get_alternate_list();

          if( 'number of alternate contacts' == $value )
          {
            $this->$key = count( $alternate_list );
          }
          else if(
            preg_match( '/alternate([0-9]+) (first_name|last_name|phone)/', $value, $matches ) )
          {
            $alt_number = intval( $matches[1] );
            $aspect = $matches[2];

            if( count( $alternate_list ) < $alt_number )
            {
              $this->$key = '';
            }
            else
            {
              if( 'phone' == $aspect )
              {
                $phone_list = $alternate_list[$alt_number - 1]->get_phone_list();
                $this->$key = is_array( $phone_list ) ? $phone_list[0]->number : '';
              }
              else
              {
                $this->$key = $alternate_list[$alt_number - 1]->$aspect;
              }
            }
          }
        }
        else if( preg_match( '/secondary (first_name|last_name)/', $value ) )
        {
          $aspect = str_replace( ' ', '_', $value );
          if( array_key_exists( $aspect, $_COOKIE ) ) $this->$key = $_COOKIE[$aspect];
        }
        else if( 'previously completed' == $value )
        {
          // no need to set the token sid since it should already be set before calling this method
          $tokens_mod = lib::create( 'database\modifier' );
          $tokens_mod->where( 'token', 'like', $token_part.'%' );
          $tokens_mod->where( 'completed', '!=', 'N' );
          $this->$key = static::count( $tokens_mod );
        }
      }
    }
  }

  /**
   * Returns the token name for a particular interview.
   * If the survey's phase is repeated then the assignment must also be provided.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\interview $db_interview 
   * @param database\interview $db_assignment (only used if the phase is repeated)
   * @static
   * @access public
   */
  public static function determine_token_string( $db_interview, $db_assignment = NULL )
  {
    return sprintf( '%s_%s',
                    $db_interview->id,
                    is_null( $db_assignment ) ? 0 : $db_assignment->id );
  }

  /**
   * The name of the table's primary key column.
   * @var string
   * @access protected
   */
  protected static $primary_key_name = 'tid';
}
