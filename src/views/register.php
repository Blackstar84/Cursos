<?php include 'header.php'; ?>
<div class="register-logo">
    <a href="#"><b>Register</b>Page</a>
</div>
<div class="card">
    <div class="card-body register-card-body">
        <p class="login-box-msg">Register a new membership</p>
        <form action="/courses/public/index.php" method="post">
            <div class="input-group mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-user"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-8">
                    <div class="icheck-primary">
                        <input type="checkbox" id="agreeTerms" name="terms" value="agree" required>
                        <label for="agreeTerms">
                            I agree to the <a href="#">terms</a>
                        </label>
                    </div>
                </div>
                <div class="col-4">
                    <button type="submit" name="register" class="btn btn-primary btn-block">Register</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>
