<?php

$password = 'changeme';
$raw = file_get_contents("php://input");
$request = json_decode($raw, true);

function set($k, $v = null) {
  session_start();
  if(!is_null($v)) {
    $_SESSION[$k] = $v;
  }
  session_write_close();
  return $_SESSION[$k];
}

function get($k) {
  return set($k);
}

function res($res, $data = '') {
  echo json_encode([
    'res' => $res,
    'data' => $data
  ]);
  die;
}

if(    !$request
    || !array_key_exists('func', $request)
    || !array_key_exists('data', $request)
  ) {
  res(false, 'Bad request');
}

$func = $request['func'];

if($func == 'login') { res(set('phprun_auth', ($request['data'] == $password) )); }
if($func == 'run' && get('phprun_auth')) { 
  $code = $request['data'];
  try {
    if(!preg_match('/return /', $code)) {
      $lines = explode('\n', $code);
      $last = array_pop($lines);
      $lines[] = 'return ' . $last;
      $code = implode('\n', $lines);
    }
    $res = @eval($code);
  } catch(exception $ex) { }

  res(true, $res); 
}

res(false, 'command not known');

