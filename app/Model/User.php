<?php

    App::uses('AppModel', 'Model');
    App::uses('Security', 'Utility');
    
    class User extends AppModel {

        public $hasOne = array(
            'UserDetail' => array(
                'className' => 'UserDetail',
                'foreignKey' => 'user_id'
            )
        );


        public $hasMany = array(
            'SentMessage' => array(
                'className' => 'Message',
                'foreignKey' => 'from_user_id'
            ),
            'ReceivedMessage' => array(
                'className' => 'Message',
                'foreignKey' => 'to_user_id'
            )
        );


        public $validate = array(
            'email' => array(
                'required' => array(
                    'rule' => 'notBlank',
                    'message' => 'Email is required',
                ),
                'validEmail' => array(
                    'rule' => 'email',
                    'message' => 'Please enter a valid email address'
                ),
                'isUnique' => array(
                    'rule' => 'isUnique',
                    'message' => 'This email address has already been taken'
                )
            ),
            'password' => array(
                'rule' => 'notBlank',
                'message' => 'Password is required',
            ),
            'confirm_password' => array(
                'rule' => array('comparePasswords'),
                'required' => true,
                'message' => 'Passwords do not match'
            ),
        );
        
        public function beforeSave($options = array()) {
            if (isset($this->data[$this->alias]['password'])) {
                $this->data[$this->alias]['password'] = Security::hash($this->data[$this->alias]['password'], 'sha1', true);
            }
            return true;
        }

        public function comparePasswords($check) {
            $password = $this->data['User']['password'];
            $confirmPassword = $this->data['User']['confirm_password'];
    
            return $password === $confirmPassword;
        }

    }
?>