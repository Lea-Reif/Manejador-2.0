<?php $this->extend('layout'); ?>

<?php $this->section('content'); ?>

<div id="login">
        <div class="container">
            <div id="login-row" class="row justify-content-center align-items-center">
                <div id="login-column" class="col-md-6">
                    <div id="login-box" class="col-md-12">
                        <form id="login-form" class="form" action="<?php echo base_url('/Home/login') ?>" method="post">
                            <h3 class="text-center text-light">Login</h3>
                            <div class="form-group">
                                <label for="password" class="text-light">Password:</label><br>
                                <input type="password" name="password" id="password" class="form-control ">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->endSection() ?>
