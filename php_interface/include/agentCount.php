<?
function CheckUserCount()
{
    $timestamp_from = COption::GetOptionString('main', 'timestamp_from_opt');
    if(!$timeFrom)
    {
        $timestamp_from = 0;
    }
    
    $timestamp_from_conv = ConvertTimeStamp($timestamp_from, 'FULL', LANGUAGE_ID);
    
    $uFilter = array('DATE_REGISTER_1' => $timestamp_from_conv);
    $resUsers = CUser::GetList(($by='id'), ($order='asc'), $uFilter);
    $usersCount = $resUsers->SelectedRowsCount();
    
    $eFilter = array('GROUPS_ID' => array(ADMIN_GROUP));
    $resEmails = CUser::GetList(($by='id'), ($order='asc'), $eFilter);
    $arEmails = array();
    while($email = $resEmails->GetNext())
    {
       $arEmails[] = $email['EMAIL']; 
    }
    
    $timestamp_now = time();
    
    COption::SetOptionString('main', 'timestamp_from_opt', $timestamp_now);
    
    $daysCount = round(($timestamp_now - $timestamp_from)/(3600*24));
    
    if($arEmails)
    {
        $fields = array(
            'COUNT' => $usersCount,
            'DAYS' => $daysCount,
            'EMAIL_TO' => implode(', ', $arEmails),
        );
        
        CEvent::Send('USER_DAILY_COUNT', SITE_ID, $fields, 'Y', USER_COUNT_EMAIL_TEMPLATE_ID);
    }

    return('CheckUserCount()');
}
?>