<?php

require __DIR__ . '/../vendor/autoload.php';

use \Curl\Curl;

$curl = new Curl();

$slack_token = getenv('HUBOT_SLACK_TOKEN');

$signed_token_key = getenv('SLACK_INVITE_COMMAND_TOKEN');

if ($_POST["token"] != $signed_token_key) {
  die();
}

$invite_url = "https://irishtechcommunity.slack.com/api/users.admin.invite";

$received_email = htmlspecialchars(trim($_POST["text"]));
$received_channel_id = "C035FCDDD";

$curl->setHeader('Content-Type', 'application/x-www-form-urlencoded');
$curl->setHeader('Accept', 'application/json');

$curl->post($invite_url, array(
  "email" => $received_email,
  "channels" => $received_channel_id,
  "set_active" => "true",
  "_attempts" => 1,
  "token" => $slack_token,
));

if ($curl->error) {
  echo 'Error: '.$curl->error_code.': '.$curl->error_message;
} else {
  if ($curl->response->ok) {
    echo 'Ok, I\'ve invited '.$received_email;
  } elseif ($curl->response->error == 'already_invited') {
    echo 'That email address has already been invited!';
  } elseif ($curl->response->error == 'sent_recently') {
    echo 'Someone recently sent an invite to this email address';
  } else {
    echo 'Herp, something went wrong, here\'s what the Slack API said - '.$curl->response->error;
  }
}
