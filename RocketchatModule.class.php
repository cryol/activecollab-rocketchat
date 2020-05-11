<?php

/**
 * @Author: Anton Baranov
 * @Author: Dmitrij Omelchuk
 * @Last Modified by:   Anton Baranov
 * @Last Modified time: 2020-05-11 13:31:20
 */

class RocketChat {
  private $api_token;
  private $api_user;
  private $api_endpoint;

  function __construct($api_url, $api_token, $api_user){
    $this->api_endpoint = $api_url. '/api/v1';
    $this->api_token = $api_token;
    $this->api_user = $api_user;
  }

  public function call_get($method, $args = array(), $timeout = 10){
    return $this->request_get($method, $args, $timeout);
  }

  public function call($method, $args = array(), $timeout = 10){
    return $this->request($method, $args, $timeout);
  }

  private function request($method, $args = array(), $timeout = 10){
    $url = "{$this->api_endpoint}/{$method}";

    $headers = array();
    $headers[] = "Content-type: application/json";
    $headers[] = "X-Auth-Token: " . $this->api_token;
    $headers[] = "X-User-Id: " . $this->api_user;

    if (function_exists('curl_version')){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      $result = curl_exec($ch);
      curl_close($ch);
    } else {
      $post_data = http_build_query($args);
      $result    = file_get_contents($url, false, stream_context_create(array(
        'http' => array(
          'protocol_version' => 1.1,
          'method'           => 'POST',
          'header'           => $headers,
          'data'             => json_encode($post_data)
        ),
      )));
    }

    return $result ? json_decode($result, true) : false;
  }
  private function request_get($method, $args = array(), $timeout = 10){
    $url = "{$this->api_endpoint}/{$method}";

    $headers = array();
    $headers[] = "Content-type: application/json";
    $headers[] = "X-Auth-Token: " . $this->api_token;
    $headers[] = "X-User-Id: " . $this->api_user;

    if (function_exists('curl_version')){
      $ch = curl_init();
      // $url =  $url . '?' . http_build_query($args);
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
      curl_setopt($ch, CURLOPT_HTTPGET, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
      $result = curl_exec($ch);
      curl_close($ch);
    } else {
      $post_data = http_build_query($args);
      $result    = file_get_contents($url, false, stream_context_create(array(
        'http' => array(
          'protocol_version' => 1.1,
          'method'           => 'GET',
          'header'           => $headers,
          'data'             => json_encode($post_data)
        ),
      )));
    }
    return $result ? json_decode($result, true) : false;
  }
}

class RocketChatModule extends AngieModule {
	protected $name = 'rocketchat';
	protected $version = '1.0';

	var $is_system = false;

	function defineRoutes() {
	}

	function defineHandlers() {
/*
		EventsManager::listen('on_project_created', 'on_project_created');
		EventsManager::listen('on_project_deleted', 'on_project_deleted');
*/
		EventsManager::listen('on_object_inserted', 'on_object_inserted');
		EventsManager::listen('on_object_updated', 'on_object_updated');
		EventsManager::listen('on_object_deleted', 'on_object_deleted');
		EventsManager::listen('on_object_opened', 'on_object_opened');
		EventsManager::listen('on_object_completed', 'on_object_completed');
	}

	function getDisplayName() {
		return lang('RocketChat');
	}

	function getDescription() {
		return lang('Display notifications in Rocketchat.');
	}

	function getUninstallMessage() {
		return lang('Rocketchat will be deactivated. Are you sure?');
	}
}
