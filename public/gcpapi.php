<?php

/*
API受信データ
Array
(
    [webhook_setting_id] => 4849
    [webhook_event_type] => message_created
    [webhook_event_time] => 1563273274
    [webhook_event] => Array
        (
            [message_id] => 1202925021782122496
            [room_id] => 157909409
            [account_id] => 1987649
            [body] => はしとちはとちは
            [send_time] => 1563273274
            [update_time] => 0
        )

)
*/
require "chatwork_api.php";
function main() {

   $raw = file_get_contents('php://input'); 
   $api = new ChatWorkAPI();

   $requestSignature = $_SERVER['HTTP_X_CHATWORKWEBHOOKSIGNATURE'];
   if ($expectedSignature != $api->auth($raw)) {
      exit(1);
   }

   $help_message = "                        
   bot check                                
      オートスケール設定確認                
                                            
   bot event                                
      イベント用インスタンス数調整          
                                            
   bot noevent                              
      イベント用インスタンス数を標準に戻す  
";                                          

   $receive = json_decode($raw, true);          
   $message = $receive['webhook_event']['body'];
   $keywords = preg_split("/\s+/", $message);   
   if ($keywords[0] != "bot") {       
      exit(2);
   }
   switch($keywords[1]) {
      case "check":                                             
         $api->check();
         break;                                                 
      case "event":                                             
         $api->event();
         break;                                                 
      case "noevent":                                           
         $api->noevent();
         break;                                                 
      case "help":                                              
         $api->help($help_message);
         break;                                                 
   }
}

main();

