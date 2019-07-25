<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die()?>

<!--<pre><?var_dump($arResult)?></pre>-->

<ul>
	<?foreach($arResult['FIRMS'] as $item):?>
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