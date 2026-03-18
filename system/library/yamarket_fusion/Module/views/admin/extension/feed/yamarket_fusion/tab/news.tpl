<?php foreach ($error as $error_text): ?>
    <div class="alert alert-danger alert-dismissible">
        <i class="fa fa-exclamation-circle"></i>
        <?= $error_text; ?>
        <button type="button" class="close" data-dismiss="alert">
            <span class="pe-7s-close"></span></button>
    </div>
<?php endforeach; ?>
<div class="container">
    <div class="content">
        <div id="aside-center">
            <form action="<?php echo $action; ?>" method="POST" id="form-news" class="form-horizontal">
                <div class="card" id="step2">
                    <div class="body-card">
                        <!-- Start content-->
                        <div id="news" name="news">

                        </div>
                    </div>
                </div>
                <script>
                    $(function Updatenews() {
                        // $.ajax({
                        //     url: 'https://opencartmodul.ru/service/news.php?modul_code=export_lite',
                        //     success: function(html) {
                        //         $('#news').html(html);
                        //     }
                        // })
                    });
                </script>
            </form>
        </div>
    </div>
</div>