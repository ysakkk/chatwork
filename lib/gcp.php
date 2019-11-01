<?php

/*
  Compute Engine
  API とリファレンス
  全てのAPI
  Compute Engine クライアント ライブラリ
  Google API PHP クライアント ライブラリ	PHP	Google	ウェブサイト	ドキュメント
  https://github.com/googleapis/google-api-php-client/blob/master/README.md

*/

require_once '../vendor/autoload.php';

class GCP_Autoscaler {

   
  private $config = "../config/autoscale.yml";
  private $secret = "../config/secret.json";

  public function __construct() {
     $this->client = new Google_Client();
     $this->client->setAuthConfig($this->secret);
     $this->client->setApplicationName('Google-ComputeSample/0.1');
     $this->client->useApplicationDefaultCredentials();
     $this->client->addScope('https://www.googleapis.com/auth/cloud-platform');
     $this->service = new Google_Service_Compute($this->client);
     $this->config  = yaml_parse_file($this->config);
     $this->config['target'] = "https://www.googleapis.com/compute/v1/projects/chokotto-farm/regions/" . $this->config['region'] . "asia-northeast1/instanceGroupManagers/" . $this->config['autoscale_group'];
  } 

  public function get() {
     $response = $this->service->regionAutoscalers->get(
        $this->config['project'], 
        $this->config['region'], 
        $this->config['autoscale_group']
     );
     return $response;
  }

  public function up() {
     $requestBody = new Google_Service_Compute_Autoscaler([
        'name'   => $this->config['autoscale_group'],
        'region' => $this->config['region'],
        'target' => $this->config['target'],
        'autoscalingPolicy' => [
           "minNumReplicas" => $this->config['up_min'],
           "maxNumReplicas" => $this->config['up_max'],
           "coolDownPeriodSec" => $this->config['cooldown'],
           "cpuUtilization" => [
              "utilizationTarget" => $this->config['cpu_target']
           ]
        ]
     ]);
     
     $response = $this->service->regionAutoscalers->update(
        $this->config['project'], 
        $this->config['region'], 
        $requestBody
     );
     
     return $response;
  }

  public function down() {
     $requestBody = new Google_Service_Compute_Autoscaler([
        'name'   => $this->config['autoscale_group'],
        'region' => $this->config['region'],
        'target' => $this->config['target'],
        'autoscalingPolicy' => [
           "minNumReplicas" => $this->config['min'],
           "maxNumReplicas" => $this->config['max'],
           "coolDownPeriodSec" => $this->config['cooldown'],
           "cpuUtilization" => [
              "utilizationTarget" => $this->config['cpu_target']
           ]
        ]
     ]);
     
     $response = $this->service->regionAutoscalers->update(
        $this->config['project'], 
        $this->config['region'], 
        $requestBody
     );
     
     return $response;
  }


}
