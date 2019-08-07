<?
define("IBLOCK_ID_NEWS_COMPARE", 8);
if(!$USER->IsAuthorized())
{
	CJSCore::Init();
}

if(isset($_GET["ID"]))
{
	if(Loader::includeModule("iblock"))
	{
		if($USER->IsAuthorized())
		{
			$user = $USER->GetID() . " " . $USER->GetLogin() . " " . $USER->GetFullName();
		}
		else
		{
			$user = "Не авторизован";
		}
		$el = new CIBlockElement();
		$arFields = [
			"IBLOCK_ID" => IBLOCK_ID_NEWS_COMPARE,
			"NAME" => "Жалоба на новость " . $_GET["ID"],
			"ACTIVE" => "Y",
			"PROPERTY_VALUES" => [
				"USER" => $user,
				"NEWS" => $_GET["ID"]
			],
			"ACTIVE_FROM" => date("d.m.Y H:i:s", strtotime("now"))
		];
	}
	if($PRODUCT_ID = $el->Add($arFields))
	{
		if($_GET["TYPE"] == "AJAX")
		{
			$GLOBALS["APPLICATION"]->RestartBuffer();
			echo json_encode(["ID" => $PRODUCT_ID]);
			die();
		}
		elseif($_GET['TYPE'] == 'GET')
		{
			if(isset($_GET["ID"])){?>
				<script>
					var textEl = document.getElementById('report_responce');
					textEl.innerText = "Ваше мнение учтено, № " + <?=$PRODUCT_ID?>;
				</script>
			<?}else{?>
				<script>
					var textEl = document.getElementById('report_responce');
					textEl.innerText = "Ошибка!";
				</script>
				<?
			}
		}
	}
}
?>

<!--	AJAX -->
<div>
		<?if($arParams["REPORT_AJAX"] === "Y"):?>
			<script>
				BX.message({
					OPINION:<?=Loc('MESS_T_RESPONSE_OPINION');?>,
					ERROR_OPINION:<?=Loc('MESS_T_ERROR_AJAX');?>
				});
				BX.ready(function () {
					var report_link = document.getElementById('report_ajax');
					report_link.onclick = function (e) {
						e.preventDefault();
						BX.ajax.loadJSON(
							"<?=$APPLICATION->GetCurPage();?>",
							{
								TYPE: "AJAX",
								ID: <?=$arResult["ID"]?>
							},
							function (data) {
								var responce_text = document.getElementById('report_responce');
								responce_text.innerText = BX.message('OPINION') + data["ID"];
							},
							function(data) {
								var responce_text = document.getElementById('report_responce');
								responce_text.innerText = BX.message('ERROR_OPINION');
							}
						)
					}
				});
			</script>
			<a id="report_ajax" href="<?=$APPLICATION->GetCurPage();?>"><?=Loc::getMessage('MESS_T_COMPLAIN');?></a>
		<?else:?>
			<a href="<?=$APPLICATION->GetCurPage() . '?TYPE=GET&ID='. $arResult["ID"]?>"><?=Loc::getMessage('MESS_T_COMPLAIN');?></a>
		<?endif;?>
		<div id="report_responce"></div>
	</div>
<!--AJAX END -->