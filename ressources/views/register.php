<h1>Register</h1>
<?php
if( isset($message)){
    echo "<p class='text-danger'>
    $message
</p>";
}
?>

<form action="/register" method="post">
    <?= \App\lscore\Application::$app->csrfToken->loadToken() ?>
    <div class="mb-3">
        <label>first name</label>
        <input type="text" name="first_name" class="form-control <?= (isset($errors['first_name']) ) ? 'is-invalid' : ''  ?>">
        <?php
        if(isset($errors['first_name'])){
            echo "<span class='invalid-feedback' role='alert'>
                        <strong> ".$errors['first_name'][0]."  </strong>
                    </span>";
        }
        ?>
    </div>
    <div class="mb-3">
        <label>last name</label>
        <input type="text" name="last_name" class="form-control <?= (isset($errors['last_name']) ) ? 'is-invalid' : ''  ?>">
        <?php
        if(isset($errors['last_name'])){
            echo "<span class='invalid-feedback' role='alert'>
                        <strong> ".$errors['last_name'][0]."  </strong>
                    </span>";
        }
        ?>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="text" name="email" class="form-control <?= (isset($errors['email']) ) ? 'is-invalid' : ''  ?>">
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
        <input type="password" name="password" class="form-control <?= (isset($errors['password']) ) ? 'is-invalid' : ''  ?>">
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