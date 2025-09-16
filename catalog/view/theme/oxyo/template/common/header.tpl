<!DOCTYPE html>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie8"><![endif]-->
<!--[if IE 9 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<!--<![endif]-->
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?><meta name="description" content="<?php echo $description; ?>" /><?php } ?>
<?php if ($keywords) { ?><meta name="keywords" content= "<?php echo $keywords; ?>" /><?php } ?>
<!-- Load essential resources -->
<script src="catalog/view/javascript/jquery/jquery-2.1.1.min.js"></script>
<link href="catalog/view/javascript/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />
<script src="catalog/view/javascript/bootstrap/js/bootstrap.min.js"></script>
<script src="catalog/view/theme/oxyo/js/slick.min.js"></script>
<script src="catalog/view/theme/oxyo/js/oxyo_common.js"></script>
<!-- Main stylesheet -->
<link href="catalog/view/theme/oxyo/stylesheet/stylesheet.css" rel="stylesheet">
<!-- Mandatory Theme Settings CSS -->
<style id="oxyo-mandatory-css"><?php echo $oxyo_mandatory_css; ?></style>
<!-- Plugin Stylesheet(s) -->
<?php foreach ($styles as $style) { ?>
<link href="<?php echo $style['href']; ?>" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<!-- Pluing scripts(s) -->
<?php foreach ($scripts as $script) { ?>
<script src="<?php echo $script; ?>"></script>
<?php } ?>
<!-- Page specific meta information -->
<?php foreach ($links as $link) { ?>
<?php if ($link['rel'] == 'image') { ?>
<meta property="og:image" content="<?php echo $link['href']; ?>" />
<?php } else { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<?php } ?>
<!-- Analytic tools -->
<?php foreach ($analytics as $analytic) { ?>
<?php echo $analytic; ?>
<?php } ?>
<?php if (isset($oxyo_styles_status)) { ?>
<!-- Custom Color Scheme -->
<style id="oxyo-color-scheme"><?php echo $oxyo_styles_cache; ?></style>
<?php } ?>
<?php if (isset($oxyo_typo_status)) { ?>
<!-- Custom Fonts -->
<style id="oxyo-fonts"><?php echo $oxyo_fonts_cache; ?></style>
<?php } ?>
<?php if ($direction == 'rtl') { ?>
<link href="catalog/view/theme/oxyo/stylesheet/rtl.css" rel="stylesheet">
<?php } ?>
<?php if ($oxyo_custom_css_status) { ?>
<!-- Custom CSS -->
<style id="oxyo-custom-css">
<?php echo $oxyo_custom_css; ?>
</style>
<?php } ?>
<?php if ($oxyo_custom_js_status) { ?>
<!-- Custom Javascript -->
<script>
<?php echo $oxyo_custom_js; ?>
</script>
<?php } ?>
</head>
<body class="<?php echo $class; ?><?php echo $oxyo_body_class; ?>">
<?php require_once('catalog/view/theme/oxyo/template/common/mobile-nav.tpl'); ?>
<div class="outer-container main-wrapper">
<?php if ($notification_status) { ?>
<div class="top_notificaiton">
  <div class="container<?php if ($top_promo_close) echo ' has-close'; ?> <?php echo $top_promo_width; ?> <?php echo $top_promo_align; ?>">
    <div class="table">
    <div class="table-cell w100"><div class="ellipsis-wrap"><?php echo $top_promo_text; ?></div></div>
    <?php if ($top_promo_close) { ?>
    <div class="table-cell text-right">
    <a onClick="addCookie('oxyo_top_promo', 1, 30);$(this).closest('.top_notificaiton').slideUp();" class="top_promo_close">&times;</a>
    </div>
    <?php } ?>
    </div>
  </div>
</div>
<?php } ?>
<?php require_once('catalog/view/theme/oxyo/template/common/headers/' . $oxyo_header . '.tpl'); ?>
<!-- breadcrumb -->
<div class="breadcrumb-holder">
<div class="container">
<span id="title-holder">&nbsp;</span>
<div class="links-holder">
<a class="oxyo-back-btn" onClick="history.go(-1); return false;"><i></i></a><span>&nbsp;</span>
</div>
</div>
</div>
<div class="container">
<?php echo $position_top; ?>
</div>