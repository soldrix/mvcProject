<div class="d-flex justify-content-center w-100 flex-column" style="min-height: calc(var(--mainHeight) - var(--navHeight));">
    <h1 class="align-self-center"><?= $exceptions->getCode() . " - " . $exceptions->getMessage()  ?></h1>
    <?php
    if($exceptions->getCode() === 301){
        Header('location: /');
    }
    ?>
</div>

