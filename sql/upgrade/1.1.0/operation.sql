INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "source_survey", "delete", true, "Removes a phase's source-specific survey from the system." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "source_survey", "edit", true, "Edits the details of a phase's source-specific survey." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "source_survey", "new", true, "Creates a new source-specific survey for a phase." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "source_survey", "add", true, "View a form for creating new source-specific survey for a phase." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "source_survey", "view", true, "View the details of a phase's particular source-specific survey." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "source_survey", "list", true, "Lists a phase's source-specific survey entries." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "source_survey", "primary", true, "Retrieves base source-specific survey information." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "phase", "add_source_survey", true, "A form to add a new source-specific survey to the phase." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "phase", "delete_source_survey", true, "Remove a phase's source-specific survey." );

INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "source_withdraw", "delete", true, "Removes a questionnaire's source-specific withdraw survey from the system." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "source_withdraw", "edit", true, "Edits the details of a questionnaire's source-specific withdraw survey." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "source_withdraw", "new", true, "Creates a new source-specific withdraw survey for a questionnaire." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "source_withdraw", "add", true, "View a form for creating new source-specific withdraw survey for a questionnaire." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "source_withdraw", "view", true, "View the details of a questionnaire's particular source-specific withdraw survey." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "source_withdraw", "list", true, "Lists a questionnaire's source-specific withdraw survey entries." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "pull", "source_withdraw", "primary", true, "Retrieves base source-specific withdraw survey information." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "widget", "qnaire", "add_source_withdraw", true, "A form to add a new source-specific withdraw survey to the questionnaire." );
INSERT IGNORE INTO operation( type, subject, name, restricted, description )
VALUES( "push", "qnaire", "delete_source_withdraw", true, "Remove a questionnaire's source-specific withdraw survey." );
