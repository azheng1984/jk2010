<?php
class WelcomeCommand {
  public $baseEvent;
  public $event;
  public $socket;

  public function execute() {
    Segmentation::initialize();
    $GLOBALS['buffer'] = array();
    $this->socket = stream_socket_server('tcp://0.0.0.0:8080', $errno, $errstr);
    stream_set_blocking($this->socket, 0);
    $this->baseEvent = event_base_new();
    $this->event = event_new();
    event_set($this->event, $this->socket, EV_READ | EV_PERSIST, array($this, 'onAccept'));
    event_base_set($this->event, $this->baseEvent);
    event_add($this->event);
    event_base_loop($this->baseEvent);
  }

  public function onAccept($socket, $flag) {
    $connection = stream_socket_accept($socket);
    stream_set_blocking($connection, 0);
    $buffer = event_buffer_new($connection, array($this, 'onRecive'), null, array($this, 'onError'),  $connection);
    event_buffer_base_set($buffer, $this->baseEvent);
    event_buffer_timeout_set($buffer, 60, 60);
    event_buffer_watermark_set($buffer, EV_READ, 0, 0xffffff);
    //event_buffer_priority_set($buffer, 10);
    event_buffer_enable($buffer, EV_READ | EV_PERSIST);
    $GLOBALS['buffer'][(int)$connection] = $buffer;
  }

  public function onError($buffer, $error, $connection) {
    $this->close($buffer, $connection);
  }

  public function onRecive($buffer, $connection) {
    $input = '';
    while (($read = event_buffer_read($buffer, 256)) !== '') {
      $input .= $read;
    }
    if ($input !== '') {
      $result = Segmentation::execute($input);
      fwrite($connection, $result);
    }
    $this->close($buffer, $connection);
  }

  private function close($buffer, $connection) {
    unset($GLOBALS['buffer'][(int)$connection]);
    event_buffer_disable($buffer, EV_READ | EV_WRITE);
    event_buffer_free($buffer);
    fclose($connection);
  }
}