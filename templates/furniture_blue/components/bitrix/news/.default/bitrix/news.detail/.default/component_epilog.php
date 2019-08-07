<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if (!empty($arResult['CANONICAL'])) {
    $APPLICATION -> SetPageProperty('ID_CAN', '<link rel="canonical" href="' . $arResult['CANONICAL'] . '">');
}


if(isset($_GET["ID"]))
{
    global $USER;
    $newElement = new CIBlockElement;
    
    $date = date('d.m.y H:m:s', time());
    
    $idNew = $arResult['ID'];
    $idName = $arResult['NAME'];
    
    if($USER->IsAuthorized())
    {
        $uId = $USER->GetId();
        $uLogin = $USER->GetLogin();
        $uFullName = $USER->GetFullName();
        
        $uInfo = $uId.' '.$uLogin.' '.$uFullName;
    }
    else
    {
        $uInfo = 'не авторизован';
    }
    
    $arFields = array(
         'IBLOCK_ID' => 8,
         'NAME' => 'Жалоба',
         'ACTIVE' => 'Y',
         'ACTIVE_FROM' => $date,
         'PROPERTY_VALUES' => array('USER'=>$uInfo, 'NEW'=>$idNew),
    );
    
    $idReport = $newElement->Add($arFields);
    
    if($idReport)
	{
		if($_GET["TYPE"] == "AJAX")
		{
			$GLOBALS["APPLICATION"]->RestartBuffer();
			echo json_encode(["ID" => $idReport]);
			die();
		}
		elseif($_GET['TYPE'] == 'GET')
		{
			if(isset($_GET["ID"])){?>
				<script>
					var textEl = document.getElementById('report_responce');
					textEl.innerText = "Ваше мнение учтено, № " + <?=$idReport?>;
				</script>
			<?}else{?>
				<script>
					var textEl = document.getElementById('report_responce');
					textEl.innerText = "Ошибка! GET TYPE";
				</script>
				<?
			}
		}
	}

}
?>