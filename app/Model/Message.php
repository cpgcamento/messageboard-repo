<?php

    App::uses('AppModel', 'Model');

    class Message extends AppModel {

        public $belongsTo = array(
            'Sender' => array(
                'className' => 'UserDetail',
                'foreignKey' => 'from_user_id'
            ),
            'Recipient' => array(
                'className' => 'UserDetail',
                'foreignKey' => 'to_user_id'
            )
        );
    }

?>