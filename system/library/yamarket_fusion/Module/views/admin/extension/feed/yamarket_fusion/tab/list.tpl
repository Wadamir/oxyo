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
            <form action="<?php echo $action; ?>" method="POST" id="form-list" class="form-horizontal">
                <div class="card" id="step2">
                    <div class="body-card">
                        <!-- Start content-->
                        <div class="tabs">
                            <input type="radio" name="inset" value="" id="tab_1" checked>
                            <label for="tab_1">Основная инструкция</label>

                            <input type="radio" name="inset" value="" id="tab_2">
                            <label for="tab_2">Инструкция к расширениям</label>

                            <input type="radio" name="inset" value="" id="tab_3">
                            <label for="tab_3">FAQ</label>

                            <div id="instructions_main">

                            </div>
                            <div id="instructions_addons">

                            </div>
                            <div id="faq">

                            </div>
                        </div>
                    </div>
                </div>
                <style type="text/css">
                    .tabs {
                        width: 100%;
                        padding: 0px;
                        margin: 0 auto;
                    }

                    .tabs>input {
                        display: none;
                    }

                    .tabs>div {
                        display: none;
                        padding: 12px;
                        /*border: 1px solid #C0C0C0;*/
                        background: #FFFFFF;
                    }

                    .tabs>label {
                        border-radius: 6px;
                        text-align: center;
                        border: none;
                        font-weight: 600;
                        padding: 10px 25px;
                        background: #9dc5de;
                        color: #2986ff;
                    }

                    .tabs>input:checked+label {
                        border-radius: 6px;
                        text-align: center;
                        border: none;
                        font-weight: 600;
                        padding: 10px 25px;
                        background: #2986ff;
                        color: #fff;
                    }

                    #tab_1:checked~#instructions_main,
                    #tab_2:checked~#instructions_addons,
                    #tab_3:checked~#faq {
                        display: block;
                    }
                </style>
                <script>
                    $(function Updateinstructions() {
                        // $.ajax({
                        //     url: 'https://opencartmodul.ru/service/marketplace/instructions_main.php?modul_code=export_lite',
                        //     success: function(html) {
                        //         $('#instructions_main').html(html);
                        //     }
                        // })
                        // $.ajax({
                        //     url: 'https://opencartmodul.ru/service/marketplace/instructions_addons.php?modul_code=export_lite',
                        //     success: function(html) {
                        //         $('#instructions_addons').html(html);
                        //     }
                        // })
                        // $.ajax({
                        //     url: 'https://opencartmodul.ru/service/marketplace/faq.php?modul_code=export_lite',
                        //     success: function(html) {
                        //         $('#faq').html(html);
                        //     }
                        // })
                    });
                </script>
            </form>
        </div>
    </div>
</div>