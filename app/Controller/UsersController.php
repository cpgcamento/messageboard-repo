<?php

    class UsersController extends AppController {

        public $uses = array('User', 'UserDetail');

        public function index() {

            $isLoggedIn = false;

            if($this->Auth->loggedIn()) {
                $isLoggedIn = true;
                $userData = $this->Auth->user();

                $age = $this->calculateAge('1998/03/16');
                if (isset($userData['UserDetail'])) {
                    $userDataDetail = $userData['UserDetail'];
                } else {
                    $userDataDetail = $userData;
                }

                if (isset($userData['User'])) {
                    $userDataVar = $userData['User'];
                } else {
                    $userDataVar = $userData;
                }

                if (isset($userDataDetail['profile'])) {
                    $profilePicture = $userDataDetail['profile'];
                } else {
                    $profilePicture = 'avatar.jpg';
                }
                
                $joinedDate = $this->dateTimeFormat($userDataDetail['created']);


                $lastActivity =  $userDataVar['last_activity'];

                $lastLoggedIn = $this->dateTimeFormat($lastActivity);
            }
            $this->set(compact('isLoggedIn','userData','age','lastLoggedIn','joinedDate','profilePicture'));

        }

        public function register() {

            if ($this->Auth->loggedIn()) {
                return $this->render('thankyou');
            }

            if ($this->request->is('post')) {

                $this->User->set($this->request->data['User']);
                $userValidates = $this->User->validates();
                $this->UserDetail->set($this->request->data['UserDetail']);
                $userDetailValidates = $this->UserDetail->validates();

                $validationErrors = array_merge($this->UserDetail->validationErrors,$this->User->validationErrors);
                
                if ($userValidates && $userDetailValidates) {

                    // Begin a transaction
                    $this->User->begin();
                    $this->UserDetail->begin();

                    try {

                        if ($this->User->save($this->request->data['User'])) {
                            $this->request->data['UserDetail']['user_id'] = $this->User->id;
                            $this->UserDetail->save($this->request->data['UserDetail']);
                            if ($this->UserDetail->save($this->request->data['UserDetail'])) {
                                $this->User->commit();
                                $this->UserDetail->commit();
                                $this->Auth->login($this->User->findById($this->User->id)); 
                                $this->render('thankyou');
                            }
                        }

                    } catch (Exception $e) {
                        $this->User->rollback();
                        $this->UserDetail->rollback();
                        $this->Session->setFlash('Registration failed. Please try again.');
                    }
                    
                } else {

                    $this->set('errors', $validationErrors);

                }


            }

        }

        public function edit() {
            if (isset($this->Auth->user()['User']['id'])) {
                $authId = $this->Auth->user()['User']['id'];
            } else {
                $authId = $this->Auth->user('id');
            }
            
            $userDetailsData = $this->User->UserDetail->findByUserId($authId);

            if ($userDetailsData['UserDetail']['profile']) {
                $profilePicture = $userDetailsData['UserDetail']['profile'];
            } else {
                $profilePicture = 'avatar.jpg';
            }

            $this->set(array(
                'userData'=> $userDetailsData,
                'profilePicture' => $profilePicture
            ));
            
            if (!$userDetailsData) {
                $this->redirect(array('controller' => 'users', 'action' => 'index'));
            }
            
            if ($this->request->is('ajax')) {
                $this->autoRender = false;
                $this->request->data['UserDetail']['id'] = $userDetailsData['UserDetail']['id'];
                $this->UserDetail->set($this->request->data);
                $file = $this->request->data['UserDetail']['profile'];
                
                if (!$file['name']) {
                     unset($this->request->data['UserDetail']['profile']);
                }

                $requestBirthDate = $this->request->data['UserDetail']['birthdate'];
                
                if ($this->UserDetail->validateMany($this->request->data)) {
                    
                    if ($file['name']) {
                        $filename = uniqid().$file['name'];
                        $uploadPath = WWW_ROOT . 'upload' . DS ;
                        $fileFullPath = $uploadPath . $filename;

                        if (move_uploaded_file($file['tmp_name'], $fileFullPath)) {
                            $this->request->data['UserDetail']['profile'] = $filename;
                        } else {
                            $this->request->data['UserDetail']['profile'] = $userDetailsData['UserDetail']['profile'];
                        }
                    }
                    $bdate = date_format(date_create($requestBirthDate),  'Y/m/d');
                    $this->request->data['UserDetail']['birthdate'] = $bdate;

                    if ($this->User->UserDetail->save($this->request->data)) {
                        $this->Auth->login($this->User->findById($authId));
                        echo json_encode(array('status' => 'success', 'message' => 'Updated Successfully!'));
                    } else {
                        echo json_encode(array('status' => 'error', 'message' => 'Error in updating users details!'));
                    }
                } else {
                    $validationErrors = $this->UserDetail->validationErrors;
                    echo json_encode(array('status' => 'error', 'message' => 'Validation failed', 'validationErrors' => $validationErrors));
                }
                
            } else {
                $this->request->data = $userDetailsData;
            }
        }

        public function login() {
            if ($this->Auth->user()) {
                return $this->redirect(array('action' => "index"));
            }

            if ($this->request->is('ajax')) {
                $this->autoRender = false;
                if ($this->Auth->login()) {
                    $userId = $this->Auth->user('id');
                    $this->User->id = $userId;
                    date_default_timezone_set('Asia/Manila');
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