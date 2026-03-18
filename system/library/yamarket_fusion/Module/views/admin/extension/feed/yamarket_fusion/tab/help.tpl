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
            <form action="<?php echo $action; ?>" method="POST" id="form-help" class="form-horizontal">
                <div class="card" id="step2">
                    <div class="body-card">
                        <!-- Start content-->
                        <div class="tabs">
                            <input type="radio" name="inset" value="" id="tab_4" checked>
                            <label for="tab_4">Поддержка</label>

                            <input type="radio" name="inset" value="" id="tab_5">
                            <label for="tab_5">Модули</label>

                            <input type="radio" name="inset" value="" id="tab_6">
                            <label for="tab_6">Услуги</label>

                            <div id="support">

                            </div>
                            <div id="modules">

                            </div>
                            <div id="services">

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

                    #tab_4:checked~#support,
                    #tab_5:checked~#modules,
                    #tab_6:checked~#services {
                        display: block;
                    }
                </style>
                <script>
                    $(function UpdateHelp() {
                        // $.ajax({
                        //     url: 'https://opencartmodul.ru/service/support.php?modul_code=export_lite',
                        //     success: function(html) {
                        //         $('#support').html(html);
                        //     }
                        // })

                        // $.ajax({
                        //     url: 'https://opencartmodul.ru/service/services.php?modul_code=export_lite',
                        //     success: function(html) {
                        //         $('#services').html(html);
                        //     }
                        // })

                        // $.ajax({
                        //     url: 'https://opencartmodul.ru/service/modules.php?modul_code=export_lite',
                        //     success: function(html) {
                        //         $('#modules').html(html);
                        //     }
                        // })
                    });
                </script>
            </form>
        </div>
    </div>
</div>