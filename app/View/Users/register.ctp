<div class="register-wrapper">
    <div class="wrap">
        <h2 class="text-center">Registration</h2>

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
            echo $this->Form->input('UserDetail.name', array('error'=>false, 'class'=>'form-control mb-2', 'required' => false));
            echo $this->Form->input('email', array('error'=>false, 'class'=>'form-control mb-2', 'required' => false));
            echo $this->Form->input('password', array('error'=>false, 'class'=>'form-control mb-2', 'required' => false));
            echo $this->Form->input('confirm_password', array('type' => 'password', 'label' => 'Confirm Password', 'class'=>'form-control mb-2', 'error' => false, 'required' => false));
            echo $this->Form->button('Register', array('class'=>'btn btn-primary'));
            echo $this->Form->end();
        
        ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $('form#UserRegisterForm').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var allInputsNotEmpty = form.find('input').filter(function() {
            return $(this).val() === '';
        }).length === 0;
        if (!allInputsNotEmpty) {
            alert('all field is required');
            return false;
        }

        $.ajax({
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {

                console.log(response);

                if (response.errors) {
                    $.each(response.errors, function(i, val) {
                        toastr["error"](val);
                    });
                    return false;
                }

                if (response.error) {
                    toastr["error"](response.error);
                    return false;
                }
                toastr["success"]('success');
                form.trigger("reset");
                setTimeout(() => {
                    window.location.href =
                        '<?php echo $this->Html->url(array("controller" => "users", "action" => "index")) ?>';
                }, 1000);

            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                toastr["error"]("Something went wrong! ");
            }
        });
    });
});
</script>