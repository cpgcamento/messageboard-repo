
<div class="card profile-info">
    <div class="card-header">
        <h3 class="card-title">USER PROFILE</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <img src="<?php echo $this->Html->url('/upload/' . $profilePicture); ?>" alt="Profile Picture" class="img-fluid rounded">
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <h3><?php echo $userData['UserDetail']['name'].", ".$age ?></h3>
                        <p>Gender:<?php echo $userData['UserDetail']['gender'] ?? 'N/A' ?></p>
                        <p>Birthdate: <?php echo $userData['UserDetail']['birthdate'] ?? 'N/A' ?></p>
                        <p>Joined: <?php echo $joinedDate; ?></p>
                        <p>Last Login: <?php echo $lastLoggedIn; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <p>Hubby:</p>
                        <p><?php echo $userData['UserDetail']['hubby'] ?? 'N/A' ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <a href="<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'edit'));?>" class="btn btn-primary">EDIT PROFILE</a>
        </div>
    </div>
</div>