<?php

include_once dirname(__FILE__) . '/utils.php';

$socket = stream_socket_server("tcp://0.0.0.0:8000", $errno, $errstr);

if (!$socket) {
  die("$errstr ($errno)\n");
}

$connects = array();
while (TRUE) {
  //формируем массив прослушиваемых сокетов:
  $read = $connects;
  $read [] = $socket;
  $write = $except = NULL;

  $r1 = $read;

  if (!stream_select($read, $write, $except, NULL)) {//ожидаем сокеты доступные для чтения (без таймаута)
    break;
  }


  if (in_array($socket, $read)) {//есть новое соединение
    //принимаем новое соединение и производим рукопожатие:
    if (($connect = stream_socket_accept($socket, -1)) && $info = handshake($connect)) {
      $connects[] = $connect;//добавляем его в список необходимых для обработки
      onOpen($connect, $info);//вызываем пользовательский сценарий
    }
    unset($read[array_search($socket, $read)]);
  }
  else {
    foreach ($read as $connect) {//обрабатываем все соединения
      $data = fread($connect, 100000000);

      if (!$data) { //соединение было закрыто
        fclose($connect);
        unset($connects[array_search($connect, $connects)]);
        onClose($connect);//вызываем пользовательский сценарий
        continue;
      }

      foreach ($connects as $connect2) {
        if ($connect != $connect2) {
          onMessage($connect2, $data);//вызываем пользовательский сценарий
        }
      }
    }
  }


}

fclose($socket);


//пользовательские сценарии:

function onOpen($connect, $info) {
  echo "open\n";
//  fwrite($connect, encode('{"hello":"hello"}'));
}

function onClose($connect) {
  echo "close\n";
}

function onMessage($connect, $data) {
  $data_decode = decode($data);
  echo $data_decode['payload'] . "\n";
  fwrite($connect, encode($data_decode['payload']));
}