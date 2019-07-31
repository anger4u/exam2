<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die()?>



<a href='http://localhost/ex2/simplecomp2/?F=1&clear_cache=Y'>http://localhost/ex2/simplecomp2/?F=1</a>

<p><?echo 'Метка времени: ' . time();?></p>

<ul>
	<?foreach($arResult['FIRMS'] as $item):?>
		<li>
			<span><b><?=$item['NAME']?></b></span>
			
			<ul>
				<?foreach($item['PRODUCTS'] as $prod):?>
				<?
				$this->AddEditAction($prod['ID'].$item['NAME'], $prod['ADD_LINK'], CIBlock::GetArrayByID($prod["IBLOCK_ID"], "ELEMENT_ADD"));
				$this->AddEditAction($prod['ID'].$item['NAME'], $prod['EDIT_LINK'], CIBlock::GetArrayByID($prod["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($prod['ID'].$item['NAME'], $prod['DELETE_LINK'], CIBlock::GetArrayByID($prod["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => 'точно удалить?'));
				?>
				<div id="<?=$this->GetEditAreaId($prod['ID'].$item['NAME']);?>">
					<li><?=$prod['PROPS']?></li>
				</div>
				<?endforeach;?>
			</ul>
		</li>
	<?endforeach;?>
	
	<?=$arResult["NAV_STRING"]?>
</ul>