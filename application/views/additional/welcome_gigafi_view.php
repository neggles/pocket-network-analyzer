<div class="container">
<div class="row">
<div class="col-lg-4 col-lg-offset-4">
    <div class="widget red-bg p-lg text-center">
        <div class="m-b-md">
            <div class="text-center">
                <h2>Welcome to POCKET-FI</h2>
            </div>
            <?php if ($currentJob) :?>
            <div class="alert alert-info">
                <p class="lead">
                    You are working on:
                    <u><?php echo $currentJob->name; ?></u>
                </p>
            </div>
            <?php else : ?>
            <div class="alert alert-danger">
                You must select a job before continuing.
            </div>
            <?php endif;?>
        </div>
    </div>
</div>
</div>
</div>