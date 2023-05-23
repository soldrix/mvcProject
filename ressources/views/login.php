<div class="col-12 col-lg-6 mx-auto mt-5">
        <form action="" method="post" class="d-flex flex-column align-items-center">
            <div class="mb-3">
                <input type="text" name="email" value="<?= $data->email ?? '' ?>" placeholder="Adresse mail" class="input-login <?php echo (isset($errors['email']) ) ? 'is-invalid' : ''  ?>">
                <?php
                if(isset($errors['email'])){
                    echo "<span class='invalid-feedback' role='alert'>
                            <strong> ".$errors['email'][0]."  </strong>
                        </span>";
                }
                ?>

            </div>
            <div class="mb-3">
                <input type="password" name="password" value="<?= $data->password ?? '' ?>" placeholder="Mot de passe" class="input-login <?php echo (isset($errors['password']) ) ? 'is-invalid' : ''  ?>">
                <?php
                if(isset($errors['password'])){
                    echo "<span class='invalid-feedback' role='alert'>
                        <strong> ".$errors['password'][0]."  </strong>
                    </span>";
                }
                ?>
            </div>
            <button type="submit" class="btn btn-login">Se connecter</button>
            <a class="text-center mt-2 text-decoration-none text-muted" href="/forgot-password">j’ai oublié mon mot de passe</a>
        </form>
</div>

