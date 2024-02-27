<?php

    class MessagesController extends AppController {

        public $uses = array('Message','User','UserDetail');

        public function send() {
            $users = $this->UserDetail->find('list', [
                'fields' => ['id','name'],
                'conditions' => [
                    'id !=' => $this->Auth->user('User.id')
                ]
            ]);
            $this->set('users', $users); 
            if ($this->request->is('post')) {
                $postData = $this->request->data;
                $this->request->data['Message']['from_user_id'] = $this->Auth->user('User.id');
                $this->Message->set($this->request->data);
                if ($this->Message->save($postData)) {
                    $this->Flash->success('Message sent successfully.',);
                    return $this->redirect(array('action' => 'index')); 
                } else {
                    $this->Session->setFlash('Error sending message.');
                }
            }
        
        }

        public function index() {
        }

        public function list() {
            App::uses('CakeTime', 'Utility');
            App::uses('CakeText', 'Utility');

            $this->layout = false;

            if($this->request->is('ajax')) { 
                $page = isset($this->request->query['page']) ? $this->request->query['page'] : 1;
                $keyword = isset($this->request->query['keywords']) ? $this->request->query['keywords'] : "";
                $limit = isset($this->request->query['limit']) ? $this->request->query['limit'] : 10;
                $offset = ($page - 1) * $limit;

                $latestMessages = $this->Message->find('all', array(
                    'fields' => array('MAX(Message.id) AS max_id', 'Message.from_user_id', 'Message.to_user_id', 'Message.content'),
                    'conditions' => array(
                        'OR' => array(
                            array('Message.from_user_id' => $this->Auth->user('User.id')),
                            array('Message.to_user_id' => $this->Auth->user('User.id')),
                        ),
                        array('Message.status' => 1)
                    ),
                    'group' => array('LEAST(Message.from_user_id, Message.to_user_id)', 'GREATEST(Message.from_user_id, Message.to_user_id)')
                ));
                
                $messages = [];
                $totalMessages = 0;
                if ($latestMessages) {
                    $messages = $this->Message->find('all', array(
                        'conditions' => array(
                            'Message.id IN' => Hash::extract($latestMessages, '{n}.0.max_id'),
                            'OR' => array(
                                'Sender.name LIKE' => "%$keyword%",
                                'Recipient.name LIKE' => "%$keyword%",
                            )
                        ),
                        'limit' => $limit,
                        'offset' => $offset,
                        'order' => array('Message.id DESC'),
                    ));
                    $totalMessages = $this->Message->find('count', array(
                        'conditions' => array(
                            'Message.id IN' => Hash::extract($latestMessages, '{n}.0.max_id'),
                            'Sender.name LIKE' => "%$keyword%",
                        ),
                        'order' => array('Message.id DESC'),
                    ));
                }

                $totalPages = ceil($totalMessages / $limit);

                //OUTPUT IN VIEW
                $htmlData = '<ul class="list-group" data-pages="'.($totalPages * $limit).'">';
                if(count($messages) > 0) {
                    foreach ($messages as $message) {

                        $profilePic = '';

                        $fromMe = '';
                        $fromMeClass = '';

                        if ($message['Sender']['user_id'] != $this->Auth->user('User.id')) {
                            $contactName = $message['Sender']['name'];
                            $contactId = $message['Sender']['user_id'];
                            $profilePic = $message['Sender']['profile'] ?? 'avatar.jpg';
                        } else {
                            $contactName = $message['Recipient']['name'];
                            $contactId = $message['Recipient']['user_id'];
                            $fromMe = 'You :';
                            $fromMeClass = 'lastMsg';
                            $profilePic = $message['Recipient']['profile'] ?? 'avatar.jpg';
                        }

                        $htmlData .='<li class="list-group-item" data-id="'.$message['Message']['id'].'" data-url="'.Router::url(array('controller' => 'messages','action'=>'view', $contactId)).'">';
                        $htmlData .='<div class="'.$fromMeClass.'">';
                        $htmlData .='<img src="'.Router::url('/upload/' . $profilePic).'" alt="profile" class="img-fluid">';
                        $htmlData .='<div class="w-100">';
                        $htmlData .='<p class="name">'.$contactName.'</p>';
                        $htmlData .='<p><span>'.$fromMe.'</span>'.CakeText::excerpt($message['Message']['content'], 'method', 15, '...').'</p>';
                        $htmlData .='<p class="date">Sent: '.CakeTime::niceShort($message['Message']['created_at']).'</p>';
                        $htmlData .='</div></div></li>';
                    }
                } else {
                    $htmlData  .= '<li class="list-group-item text-center">NO DATA</li>';
                }
                $htmlData .='</ul>';
                if ($totalPages > 1) {
                    $htmlData .= '<div class="text-center mt-4"><a href="javascript:void(0)" class="btn btn-primary" id="show-more" data-current="'.$limit.'">Show more</a></div>';
                }
                
                
                $this->response->type('json');
                $this->response->body(json_encode(['html' => $htmlData]));
                return $this->response;
            }
        
        }

        public function deleteUserMessage() {
            if($this->request->is('ajax')) { 

                $id = $this->request->query['id'];
                $msg = $this->Message->findById($id);
                $message = $this->Message->updateAll(
                    array('Message.status' => 0),
                    array(
                        'OR' => array(
                            array('Message.from_user_id' => $msg["Message"]['from_user_id'], 'Message.to_user_id' => $msg["Message"]['to_user_id']),
                            array('Message.from_user_id' => $msg["Message"]['to_user_id'], 'Message.to_user_id' => $msg["Message"]['from_user_id'])
                        )
                ));
                $this->response->type('json');
                $this->response->body(json_encode(['html' => 'success']));
                return $this->response;
            }
        }

        public function view($id) {

            $recipientName = $this->UserDetail->find('all', array(
                'conditions' => array('UserDetail.user_id' => $id),
                'fields' => array('UserDetail.name')
            ));

            if ($this->request->is('post')) {
                $this->request->data['Message']['from_user_id'] = $this->Auth->user('User.id');
                $this->request->data['Message']['to_user_id'] = $id;
                $this->Message->set($this->request->data);
                $this->Message->save($this->request->data);
                
                $this->response->type('json');
                $this->response->body(json_encode(['success' => 'Data saved successfully!']));
                return $this->response;
            }
            $this->set(array(
                'recipientName' => $recipientName,
                'id' => $id,
            ));
        }

        public function viewUserMessage() {
            App::uses('CakeTime', 'Utility');

            $authId = $this->Auth->user('User.id');
            $id = isset($this->request->query['id']) ? $this->request->query['id'] : $id;
            $search = isset($this->request->query['search']) ? $this->request->query['search'] : '';

            $conditions = array(
                'OR' => array(
                    array(
                        'Message.from_user_id' => $authId,
                        'Message.to_user_id' => $id,
                    ),
                    array(
                        'Message.from_user_id' => $id,
                        'Message.to_user_id' => $authId,
                    )
                ),
                'Message.content LIKE' => "%$search%",
            );

            $messages = $this->Message->find('all', array(
                'conditions' => $conditions,
            ));

            $htmlData = '<div class="data">';

            foreach ($messages as $message) {
                $profilePic = $message['Sender']['profile'] ?? 'avatar.jpg';
                if ($message['Sender']['user_id'] === $authId) {
                    $msgClasses = 'my-message';
                } else  {
                    $msgClasses = 'not-my-message';
                }
                $htmlData .='<div class="message-wrap '.$msgClasses.'">';
                $htmlData .='<div class="message-content">';
                $htmlData .='<img src="'.Router::url('/upload/'.$profilePic).'" alt="profile">';
                $htmlData .='<div><div class="messages">';
                $htmlData .='<p class="name">'.$message['Sender']['name'].'</p>';
                $htmlData .='<p class="details">'.$message['Message']['content'].'</p>';
                $htmlData .='</div> <p class="time">Sent: '.CakeTime::niceShort($message['Message']['created_at']).'</p></div>';
                $htmlData .='</div> </div>';
            }
            $htmlData .='</div>';

            $this->response->type('json');
            $this->response->body(json_encode(['html' => $htmlData]));
            return $this->response;
        }
        
    }

?>