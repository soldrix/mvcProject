<h1>Contact</h1>
<form action="/contact" method="post">
    <?= \App\lscore\Application::$app->csrfToken->loadToken() ?>
    <div class="mb-3">
        <label>Subject</label>
        <input type="text" name="subject" class="form-control">
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="text" name="email" class="form-control">
    </div>
    <div class="mb-3">
        <label>Body</label>
        <textarea  name="body" class="form-control"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>