<?php

    App::uses('ExceptionRenderer', 'Error');

    class AppError extends ExceptionRenderer {
        public function error404($error) {
            $this->controller->response->statusCode(404);
            $this->controller->layout = 'error'; 
            $this->controller->render('/Errors/error404');
            $this->controller->response->send();
        }
        public function missingController($error) {
            $this->controller->render('/Errors/error404');
        }
    }

?>