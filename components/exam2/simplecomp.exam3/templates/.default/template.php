<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<p><b><?=GetMessage("TITLE")?></b></p>

<ul>
    <?foreach($arResult['USERS'] as $kUser => $user):?>
        <li><?=$user['LOGIN']?></li>
        
        <ul>
            <?foreach($arResult['USERS'][$kUser]['USER_NEWS'] as $new):?>
                <li><?=$new['NAME']?></li>
            <?endforeach;?>
        </ul>
    <?endforeach;?>
</ul>