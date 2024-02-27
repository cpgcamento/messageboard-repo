<h2>New Messages</h2>
<div class="card">
    <div class="card-body">
        <?php
            echo $this->Form->create('Message');
            echo $this->Form->input('to_user_id', array('label' => 'Recipient', 'options' => $users, 'id' => 'selectTo'));
            echo $this->Form->input('content', array('rows' => 5, 'class' => 'form-control'));
            echo $this->Form->button('Send Message', array('class'=>'btn btn-primary mt-4'));
            echo $this->Form->end();
        ?>
    </div>
</div>
<style>
    
.select2-results__options {
    color: black;
}

.select2-selection__rendered {
    line-height: 31px !important;
}
.select2-container .select2-selection--single {
    height: 35px !important;
}
.select2-selection__arrow {
    height: 34px !important;
}
</style>
<script>
    $(document).ready(function() {
        $('select#selectTo').select2({
            width: '100%'
        });
    });
</script>