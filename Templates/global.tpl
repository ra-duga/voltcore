<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <?if (isset($css) && $css):?>
            <?foreach($css as $file):?>
            <link type="text/css" rel="stylesheet" href="<?=$file?>?<?=filemtime(PUBROOT.$file)?>" />
            <?endforeach?>
        <?endif?>
        <?if (isset($js) && $js):?>
            <?foreach($js as $file):?>
            <script type="text/javascript" src="<?=$file?>?<?=filemtime(PUBROOT.$file)?>"></script>
            <?endforeach?>
        <?endif?>
    <? if (isset($dopJs)): ?>        
        <script type="text/javascript">
            <?=$dopJs?>
        </script>
    <? endif ?>
        <title><?=$title?></title>
    </head>
    <body>
        <div id="header"><?=$header?></div>
        <div id="mainContent"><?=$content?></div>  
        <div id="footer"><?=$footer?></div>
    </body>
</html>