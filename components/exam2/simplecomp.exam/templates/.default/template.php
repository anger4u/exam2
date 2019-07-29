<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<h1><?=GetMessage('HEADER');?><?=$arResult['COUNT'];?></h1>

<ul>
	<?foreach ($arResult['NEWS_ITEMS'] as $arItem):?>
	<li>
		<b><?=$arItem['NAME']?></b> - <?=$arItem['DATE_ACTIVE_FROM']?> (<?foreach ($arItem['SECT_ITEMS'] as $section):?><?=$section['NAME']?>, <?endforeach;?>)
		
		<?foreach ($arItem['SECT_ITEMS'] as $item):?>
		<ul>
			<?foreach ($item['ELEM_ITEMS'] as $element):?>
			<li>
				<?=$element['NAME']?> - <?=$element['PROPERTY_PRICE_VALUE']?>, <?=$element['PROPERTY_MATERIAL_VALUE']?>, <?=$element['PROPERTY_ARTNUMBER_VALUE']?>
			</li>
			<?endforeach;?>
		</ul>
		<?endforeach;?>
	</li>
	<?endforeach;?>
</ul>