<style>
    .card {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-align: center;
        align-items: center;
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
        text-align:center;
        justify-content:center;
    }
</style>
<div class="login-wrapper m-auto">
    <div class="card">
        <div class="card-body">
            <h1 class="h3 mb-3 font-weight-normal">LOGIN</h1>
            <?php
                echo $this->Form->create('User', array('url' => 'login', 'class' => 'form-signin'));
                echo $this->Form->input('email', array('class' => 'form-control', 'label' => false, 'placeholder' => 'Email Address', 'required' => false));
                echo $this->Form->input('password', array('class' => 'form-control', 'label' => false, 'placeholder' => 'Password', 'required' => false));
                echo $this->Form->button('Sign In', array('class' => 'btn btn-lg btn-primary btn-block mt-2'));
                echo $this->Form->end();
                echo $this->Html->link('Register', array('controller' => 'users', 'action' => 'register'));
            ?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#UserLoginForm').on('submit', function(e){
            e.preventDefault();
            if (!$(this).find('input#UserEmail').val() || !$(this).find('input#UserPassword').val()) {
                toastr["error"]("All Fields are required");
                return false;
            }
            var form = $(this);

            $.ajax({
                type: 'POST',
                data: form.serialize(),
                dataType:'json',
                success: function(response) {
                    if (response.status === "success") { 
                        toastr["success"](response.message);
                        form.trigger("reset");
                        setTimeout(() => {
                            window.location.href = '<?php echo $this->Html->url(array("controller" => "users", "action" => "index")) ?>';
                        }, 1000);
                    } else {
                        toastr["error"](response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    toastr["error"]("Something went wrong! ");
                }
            });
        });
    });

        
</script>