<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;

class ViewPathBuilder {
    public static function build($name, $outputFormat) {
        $result = $name;
        if (Config::getBoolean(
            'hyperframework.web.view.filename.include_output_format', true
        )) {
            if ($outputFormat === null) {
                $outputFormat = Config::getString(
                    'hyperframework.web.view.default_output_format', 'html'
                );
            }
            if ($outputFormat !== '') {
                $result .= '.' . $outputFormat;
            }
        }
        $format = Config::getString(
            'hyperframework.web.view.format', 'php'
        );
        if ($format !== '') {
            $result .= '.' . $format;
        }
        return $result;
    }
}
