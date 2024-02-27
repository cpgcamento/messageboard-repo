<?php

App::uses('AppModel', 'Model');

class UserDetail extends AppModel {

    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id'
        ),
    );


    public $validate = array(
        'name' => array(
            'between' => array(
                'rule' => array('between', 5, 20),
                'message' => 'Name must be between 5 and 20 characters long'
            ),
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Name is required',
            ),
        ),
        'profile' => array(
            'rule' => array('extension', array('gif','jpeg','jpg','png')),
            'message' => 'Please upload a valid image file234',
        )
    );

}


?>