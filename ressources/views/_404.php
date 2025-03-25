<div class="d-flex justify-content-center container align-items-center" style="min-height: calc(100vh - 60px);">
    <div class='alert alert-info'>
        <p class="mb-0"><?= ($exceptions->getCode() !== 0) ? $exceptions->getCode() . " - " : "" ?><?= $exceptions->getMessage()  ?></p>
    </div>
</div>
