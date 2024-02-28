<?php

    class UsersController extends AppController {

        public $uses = array('User', 'UserDetail');

        public function index() {

            //Check if has auth
            if($this->Auth->user()) {
                //Retrieves the auth user data from session
                $userData = $this->Auth->user();
                //Initialize age to null  
                $age = null;
                //check if birtdate exist and has value
                if (isset($userData['UserDetail']['birthdate']) && $userData['UserDetail']['birthdate']) {
                    $age = $this->calculateAge($userData['UserDetail']['birthdate']);
                }
                
                if (isset($userData['UserDetail'])) {
                    $userDataDetail = $userData['UserDetail'];
                } else {
                    $userDataDetail = $userData;
                }
                
                //Create variable $userDataVar
                //default userdata value dont have array of User
                //when edit i update login auth to refresh auth data and the userdata has array of User
                if (isset($userData['User'])) {
                    $userDataVar = $userData['User'];
                } else {
                    $userDataVar = $userData;
                }
                //Put default avatar of an empty profile
                if (isset($userDataDetail['profile'])) {
                    $profilePicture = $userDataDetail['profile'];
                } else {
                    $profilePicture = 'avatar.jpg';
                }
                
                $joinedDate = $this->dateTimeFormat($userDataDetail['created']);

                $lastActivity =  $userDataVar['last_activity'];

                $lastLoggedIn = $this->dateTimeFormat($lastActivity);
            }
            $this->set(compact('userData','age','lastLoggedIn','joinedDate','profilePicture'));

        }

        public function register() {
            // View thank you page if has Auth
            if ($this->Auth->user()) {
                return $this->render('thankyou');
            }
            // Request using ajax   
            if ($this->request->is('ajax')) {
                //Set User Data for Table users
                $this->User->set($this->request->data['User']);
                //Validate user
                $userValidates = $this->User->validates();
                //Set UserDetail Data for Table user_details
                $this->UserDetail->set($this->request->data['UserDetail']);
                //Validate user details
                $userDetailValidates = $this->UserDetail->validates();
                //Merge errors
                $validationErrors = array_merge($this->UserDetail->validationErrors,$this->User->validationErrors);
                
                //Check if has Error
                if ($userValidates && $userDetailValidates) {

                    // Begin a transaction
                    $this->User->begin();

                    try {
                        // Save data for table users
                        if ($this->User->save($this->request->data['User'])) {
                            $this->request->data['UserDetail']['user_id'] = $this->User->id;
                            //Save data for table user_details
                            if ($this->UserDetail->save($this->request->data['UserDetail'])) {
                                $this->User->commit();
                                //Login new register user
                                $this->Auth->login($this->User->findById($this->User->id)); 
                                $this->response->body(json_encode(['success' => 'Successfull Registered']));
                            } else {
                                $this->User->rollback();
                                $this->response->body(json_encode(['error' => 'Error on saving user']));
                            }
                        }

                    } catch (Exception $e) {
                        //Error rollback both users and user_details table
                        $this->User->rollback();
                        $this->response->body(json_encode(['error' => 'Registration failed. Please try again.']));
                    }
                    
                } else {
                    $this->response->body(json_encode(['errors' => $validationErrors]));

                }
                $this->response->type('json');
                return $this->response;

            }

        }

        public function edit() {
            //iniatialize auth id
            $authId = $this->Auth->user('User.id');
            //retrive user detail data 
            $userDetailsData = $this->User->UserDetail->findByUserId($authId);
            //add a default avatar to all empty
            if ($userDetailsData['UserDetail']['profile']) {
                $profilePicture = $userDetailsData['UserDetail']['profile'];
            } else {
                $profilePicture = 'avatar.jpg';
            }
            //pass data to view
            $this->set(array(
                'userData'=> $userDetailsData,
                'profilePicture' => $profilePicture
            ));
            //if user not found redirect to homepage
            if (!$userDetailsData) {
                $this->redirect(array('controller' => 'users', 'action' => 'index'));
            }
            //check ajax request
            if ($this->request->is('ajax')) {
                //disable default layout in view
                $this->autoRender = false;
                //insert data id 
                $this->request->data['UserDetail']['id'] = $userDetailsData['UserDetail']['id'];
                //set data form the table user_details
                $this->UserDetail->set($this->request->data);
                //iniatialize profile picture
                $file = $this->request->data['UserDetail']['profile'];
                //if profile picture is empty unset so that it not be inluded to the validation in the model
                if (!$file['name']) {
                     unset($this->request->data['UserDetail']['profile']);
                }
                //initialise birthdate
                $requestBirthDate = $this->request->data['UserDetail']['birthdate'];
                //validate userdetails
                if ($this->UserDetail->validateMany($this->request->data)) {
                    //if has profile picture
                    if ($file['name']) {
                        $filename = uniqid().$file['name'];
                        $uploadPath = WWW_ROOT . 'upload' . DS ;
                        $fileFullPath = $uploadPath . $filename;
                        //move image to upload directory
                        if (move_uploaded_file($file['tmp_name'], $fileFullPath)) {
                            $this->request->data['UserDetail']['profile'] = $filename;
                        } else {
                            $this->request->data['UserDetail']['profile'] = $userDetailsData['UserDetail']['profile'];
                        }
                    }
                    //format date
                    $bdate = date_format(date_create($requestBirthDate),  'Y/m/d');
                    $this->request->data['UserDetail']['birthdate'] = $bdate;
                    //Save userdetail
                    if ($this->User->UserDetail->save($this->request->data)) {
                        //refresh auth
                        $this->Auth->login($this->User->findById($authId));
                        echo json_encode(array('status' => 'success', 'message' => 'Updated Successfully!'));
                    } else {
                        echo json_encode(array('status' => 'error', 'message' => 'Error in updating users details!'));
                    }
                } else {
                    //alidation errors
                    $validationErrors = $this->UserDetail->validationErrors;
                    echo json_encode(array('status' => 'error', 'message' => 'Validation failed', 'validationErrors' => $validationErrors));
                }
                
            } else {
                $this->request->data = $userDetailsData;
            }
        }

        public function login() {

            //Redirect to homepage if has auth
            if ($this->Auth->user()) {
                return $this->redirect(array('action' => "index"));
            }

            //Check if has request using ajax 
            if ($this->request->is('ajax')) {
                $this->autoRender = false;
                //Attemp to login the user
                if ($this->Auth->login()) {
                    //Once login update last_activity
                    $userId = $this->Auth->user('id');
                    $this->User->id = $userId;
                    $this->User->saveField('last_activity', date('Y-m-d H:i:s'));
                    //Refresh auth data
                    $this->Auth->login($this->User->findById($userId));
                    echo json_encode(array('status' => 'success', 'message' => 'Login Successfully!'));
                } else {
                    echo json_encode(array('status' => 'error', 'message' => 'Incorrect Credentials'));
                }
            }

        }

        public function logout() {
            return $this->redirect($this->Auth->logout());
        }

        public function calculateAge($birthdate) {
            $birthDate = new DateTime($birthdate);
            $currentDate = new DateTime();
            $age = $currentDate->diff($birthDate)->y;
            return $age;
        }

        public function dateTimeFormat($datetime) {
            $timestamp = strtotime($datetime);
            return strftime('%B %e, %Y %I:%M %p', $timestamp);
        }

    }

?>