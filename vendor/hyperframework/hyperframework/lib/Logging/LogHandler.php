<?php
namespace Hyperframework\Logging;

use Hyperframework\Common\Config;

class LogHandler {
    private $writer;
    private $formatter;

    public function handle($level, array $params) {
        $formatter = $this->getFormatter();
        $log = $formatter->format($level, $params);
        $writer = $this->getWriter();
        $writer->write($log);
    }

    private function getFormatter() {
        if ($this->formatter === null) {
            $class = Config::getString(
                'hyperframework.logging.log_formatter_class', ''
            );
            if ($class === '') {
                $this->formatter = new LogFormatter;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Log formatter class '$class' does not exist, defined "
                            . "in 'hyperframework.logging.log_formatter_class'."
                    );
                }
                $this->formatter = new $class;
            }
        }
        return $this->formatter;
    }

    private function getWriter() {
        if ($this->writer === null) {
            $class = Config::getString(
                'hyperframework.logging.log_writer_class', ''
            );
            if ($class === '') {
                $this->writer = new LogWriter;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Log writer class '$class' does not exist, defined "
                            . "in 'hyperframework.logging.log_writer_class'."
                    );
                }
                $this->writer = new $class;
            }
        }
        return $this->writer;
    }
}
