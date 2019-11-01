<?php

require "gcp.php";

class ChatWorkAPI {

   private $config = "../config/chatwork.yml";

   public function __construct() {
      $this->config  = yaml_parse_file($this->config);
      $this->gcp = new GCP_Autoscaler();
   } 

   private function send_message($reply_message) {
      $room_api = "https://api.chatwork.com/v2/rooms/" . $this->config['room_id'] . "/messages";
      $reply = array('body' => $reply_message );
      header('Content-type: application/json; charset=utf-8');
      $cdata = curl_init();
      curl_setopt($cdata, CURLOPT_URL, $room_api);
      curl_setopt($cdata, CURLOPT_HTTPHEADER, array('X-ChatWorkToken: ' . $this->config['api']));
      curl_setopt($cdata, CURLOPT_POSTFIELDS, http_build_query($reply, '', '&'));
      curl_setopt($cdata, CURLOPT_POST, 1);
      curl_setopt($cdata, CURLOPT_RETURNTRANSFER, 1);
      $ret = curl_exec($cdata);
      curl_close($cdata);
   }

   private function auth($raw) {
      $webhook_token = $this->config['webhook_token'];
      $key = base64_decode($webhook_token);
      $digest = hash_hmac('sha256', $raw, $key, TRUE);
      $expectedSignature = base64_encode($digest);
      $requestSignature = $_SERVER['HTTP_X_CHATWORKWEBHOOKSIGNATURE'];
      return $requestSignature;
   }


   public function check() {
      $this->send_message($this->gcpget());
   }

   public function event() {
        try { 
           $response = $gcp->up();
        } catch (Google_Service_Exception $e) { 
           $message = $e->getMessage();
           $this->send_message("[code]".$message."[/code]");
           break;
        }
        $this->send_message($this->gcpget());
   }

   public function noevent() {

        try { 
           $response = $gcp->down();
        } catch (Google_Service_Exception $e) { 
           $message = $e->getMessage();
           $this->send_message("[code]".$message."[/code]");
           break;
        }
   }

   public function help($help_message) {
        $this->send_message($help_message);
   }

   public function gcpget() {
     $response = $this->gcp->get();
     $res =  "min: ". $response['autoscalingPolicy']['minNumReplicas'] ."\n";
     $res .= "max: ". $response['autoscalingPolicy']['maxNumReplicas'] ."\n";
     $message = "[code]".$res."[/code]";
     return $message;
   }
  
}
