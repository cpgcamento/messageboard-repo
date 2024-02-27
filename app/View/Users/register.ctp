<div class="register-wrapper">
    <div class="wrap">
        <h2>Registration</h2>

        <?php if (isset($errors) && !empty($errors)): ?>
        <div class="error-message">
            <ul>
                <?php foreach ($errors as $field => $error): ?>
                    <?php foreach ($error as $msg): ?>
                        <li><?php echo $msg ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php

            echo $this->Form->create('User');
            echo $this->Form->input('UserDetail.name', array('error'=>false, 'required' => false));
            echo $this->Form->input('email', array('error'=>false, 'required' => false));
            echo $this->Form->input('password', array('error'=>false, 'required' => false));
            echo $this->Form->input('confirm_password', array('type' => 'password', 'label' => 'Confirm Password', 'error' => false, 'required' => false));
            echo $this->Form->end('Register');
        
        ?>
    </div>
</div>