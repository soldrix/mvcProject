<h1>Login</h1>
<form action="" method="post">
    <div class="mb-3">
        <label>Email</label>
        <input type="text" name="email" class="form-control <?php echo (isset($errors['email']) ) ? 'is-invalid' : ''  ?>">
        <?php
            if(isset($errors['email'])){
                echo "<span class='invalid-feedback' role='alert'>
            <strong> ".$errors['email'][0]."  </strong>
        </span>";
            }
        ?>

    </div>
    <div class="mb-3">
        <label>password</label>
        <input type="password" name="password" class="form-control">
        <?php
        if(isset($errors['password'])){
            echo "<span class='invalid-feedback' role='alert'>
            <strong> ".$errors['password'][0]."  </strong>
        </span>";
        }
        ?>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>