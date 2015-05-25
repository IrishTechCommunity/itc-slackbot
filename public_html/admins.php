<?php

require __DIR__ . '/../vendor/autoload.php';

use \Curl\Curl;

$curl = new Curl();

$slack_token = getenv('HUBOT_SLACK_TOKEN');

$curl->get("https://slack.com/api/users.list", array(
  "token" => $slack_token,
));

$data = $curl->response->members;

$admins = array();

foreach ($data as $member) {
  if ($member->name == 'thehubotadmin') {
    continue;
  }
  if ($member->is_admin) {
    $admins[] = array(
      "handle" => $member->name,
      "first_name" => $member->profile->first_name,
    );
  }
}

echo "> The following members have admin privileges and can assist you if you're having problems - \n";

foreach ($admins as $admin) {
  echo "> @".$admin["handle"];
  if ($admin["first_name"]) {
    echo " ".$admin["first_name"];
  }
  echo "\n";
}

$curl->close();
