<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die()?>

<!--<pre><?var_dump($arResult)?></pre>-->

<ul>
	<?foreach($arResult['FIRMS'] as $item):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<p class="news-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<li>
			<span><b><?=$item['NAME']?></b></span>
			
			<ul>
				<?foreach($item['PRODUCTS'] as $prod):?>
					<li><?=$prod?></li>
				<?endforeach;?>
			</ul>
		</li>
	<?endforeach;?>
</ul>