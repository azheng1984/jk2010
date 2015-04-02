<?php
namespace Hyperframework\Logging;

use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class LogHandler {
    private $writer;
    private $formatter;

    /**
     * @param object $logRecord
     */
    public function handle($logRecord) {
        $formatter = $this->getFormatter();
        $formattedLog = $formatter->format($logRecord);
        $writer = $this->getWriter();
        $writer->write($formattedLog);
    }

    protected function getFormatter() {
        if ($this->formatter === null) {
            $configName = 'hyperframework.logging.log_formatter_class';
            $class = Config::getString($configName, '');
            if ($class === '') {
                $this->formatter = new LogFormatter;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Class '$class' does not exist, set using config "
                            . "'$configName'."
                    );
                }
                $this->formatter = new $class;
            }
        }
        return $this->formatter;
    }

    protected function getWriter() {
        if ($this->writer === null) {
            $configName = 'hyperframework.logging.log_writer_class';
            $class = Config::getString($configName, '');
            if ($class === '') {
                $this->writer = new LogWriter;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Class '$class' does not exist,"
                            . " set using config '$configName'."
                    );
                }
                $this->writer = new $class;
            }
        }
        return $this->writer;
    }
}
