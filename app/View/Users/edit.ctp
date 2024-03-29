<div class="card profile-info">
    <div class="card-header">
        <div class="h3 card-title">User Profile - EDIT</div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-6">
                <img src="<?php echo $this->Html->url('/upload/' . $profilePicture); ?>" alt="Profile Picture">
                <button class="btn btn-info mt-2" id="upload-pic">Upload Picture</button>
            </div>
            <div class="col-sm-6">
                <?php 
                    echo $this->Form->create('User', array('type' => 'file'));
                    echo $this->Form->input('UserDetail.profile', array('type' => 'file', 'style'=>'display:none;', 'accept' => 'image/gif, image/png, image/jpeg', 'label'=>false, 'id'=>'profile-input', 'required'=>false));
                    echo $this->Form->input('UserDetail.name', array('value' => $userData['UserDetail']['name'], 'class' => 'form-control mb-2','required' => false));
                    echo $this->Form->radio('UserDetail.gender', array('Male' => 'Male', 'Female' => 'Female'), array('value' => $userData['UserDetail']['gender']));
                    echo $this->Form->input('UserDetail.birthdate', array('type'=>'text', 'id' => 'bdate', 'class' => 'form-control mb-2'));
                    echo $this->Form->input('UserDetail.hubby', array('value' => $userData['UserDetail']['hubby'], 'class' => 'form-control mb-2'));
                    echo $this->Form->input('User.email', array('class' => 'form-control mb-4','type' => "text"));
                    echo $this->Form->input('User.password', array('class' => 'form-control mb-4','value' => "", 'required' => false));
                    echo $this->Form->button('Save & Update', array('class' =>'btn btn-primary'));
                    echo $this->Form->end();
                ?>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#upload-pic').on('click', function() {
        $('#profile-input').click();
    });

    $('#bdate').datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: '1900:2050'
    });

    $('#profile-input').on('change', function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('.card.profile-info img').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    $('form#UserEditForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);

        $.ajax({
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                console.log(response)
                if (response.status === "success") {
                    toastr["success"](response.message);
                    setTimeout(() => {
                        window.location.href =
                            '<?php echo $this->Html->url(array("controller" => "users", "action" => "index")) ?>';
                    }, 1000);
                } else {
                    if (response.status === "error") {
                        $.each(response.validationError, function(i, val) {
                            toastr['error'](val);
                        });
                    } else {
                        $.each(response.validationErrors.UserDetail, function(i, val) {
                            toastr["error"](val);
                        });
                    }
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