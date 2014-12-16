<?php
namespace Hyperframework\Common;

class Inflector {
    public static function pluralize($word) {
        $word = strtolower($word);
        $result = self::getSpecialResult($word);
        if ($result !== null) {
            return $result;
        }
        static $rules = [
            '/(quiz)$/' => '\1zes',
            '/^(oxen)$/' => '\1',
            '/^(ox)$/' => '\1en',
            '/^(m|l)ice$/' => '\1ice',
            '/^(m|l)ouse$/' => '\1ice',
            '/(matr|vert|ind)(?:ix|ex)$/' => '\1ices',
            '/(x|ch|ss|sh)$/' => '\1es',
            '/([^aeiouy]|qu)y$/' => '\1ies',
            '/(hive)$/' => '\1s',
            '/(?:([^f])fe|([lr])f)$/' => '\1\2ves',
            '/sis$/' => 'ses',
            '/([ti])a$/' => '\1a',
            '/([ti])um$/' => '\1a',
            '/(buffal|tomat)o$/' => '\1oes',
            '/(bu)s$/' => '\1ses',
            '/(alias|status)$/' => '\1es',
            '/(octop|vir)i$/' => '\1i',
            '/(octop|vir)us$/' => '\1i',
            '/^(ax|test)is$/' => '\1es',
            '/s$/' => 's',
            '/$/', 's',
        ];
        return self::convert($word, $rules);
    }

    private static function convert($word, $isSingular) {
        $originalWord = $word;
        $word = strtolower($word);
        static $specialWords = [
            'atlas' => 'atlases',#?
            'beef' => 'beefs',#?
            'brother' => 'brothers', #?
            'cafe' => 'cafes', #?
    //        'child' => 'children',
            'cookie' => 'cookies', #?
            'corpus' => 'corpuses',
            'cow' => 'cows', #?
            'curve' => 'curves',#?
            'foe' => 'foes',#?
            'ganglion' => 'ganglions',
            'genie' => 'genies', #?
            'genus' => 'genera',#?
            'graffito' => 'graffiti',
            'hoof' => 'hoofs', #?
            'loaf' => 'loaves', #?
     //       'man' => 'men',
            'money' => 'money',
    //        'move' => 'moves',
            'niche' => 'niches',#?
            'opus' => 'opuses',#?
            'ox' => 'oxen', #?
    //        'sex' => 'sexes', 
            'testis' => 'testes', #?
            'turf' => 'turfs', #?
            'wave' => 'waves', #?
            'carp' => 'carp', #fish
            'chassis' => 'chassis',#?
            'clippers' => 'clippers',#?
            'cod' => 'cod', #fish
    //        'equipment' => 'equipment',
    //        'information' => 'information',
            'news' => 'news', #
            'nexus' => 'nexus', #
            'pincers' => 'pincers',
            'pliers' => 'pliers',
    //        'rice' => 'rice',
            'salmon' => 'salmon', #fish
            'scissors' => 'scissors', #
    //        'series' => 'series',
            'shears' => 'shears',
    //        'species' => 'species',
    
            'zombie' => 'zombies',
            'move' => 'moves',
            'sex' => 'sexes',
            'child' => 'children',
            'man' => 'men',
            'person' => 'people',
    
            'police' => 'police',
            'jeans' => 'jeans',
            'sheep' => 'sheep',
            'fish' => 'fish',
        ];
        $result = null;
        if ($isSingular) {
            if (isset($specialWords[$word])) {
                $result = $specialWords[$word];
            }
        } else {
            $result = array_search($word, $specialWords, true);
            if ($result !== false) {
                $result = $result;
            }
        }
        if ($result === null) {
            if ($isSingular) {
                static $pluralRules = [
                    '/(quiz)$/' => '\1zes',
                    '/^(oxen)$/' => '\1',
                    '/^(ox)$/' => '\1en',
                    '/^(m|l)ice$/' => '\1ice',
                    '/^(m|l)ouse$/' => '\1ice',
                    '/(matr|vert|ind)(?:ix|ex)$/' => '\1ices',
                    '/(x|ch|ss|sh)$/' => '\1es',
                    '/([^aeiouy]|qu)y$/' => '\1ies',
                    '/(hive)$/' => '\1s',
                    '/(?:([^f])fe|([lr])f)$/' => '\1\2ves',
                    '/sis$/' => 'ses',
                    '/([ti])a$/' => '\1a',
                    '/([ti])um$/' => '\1a',
                    '/(buffal|tomat)o$/' => '\1oes',
                    '/(bu)s$/' => '\1ses',
                    '/(alias|status)$/' => '\1es',
                    '/(octop|vir)i$/' => '\1i',
                    '/(octop|vir)us$/' => '\1i',
                    '/^(ax|test)is$/' => '\1es',
                    '/s$/' => 's',
                    '/$/', 's',
                ];
                $rules = $pluralRules;
            } else {
                static $singularRules = [
                    '/(database)s$/' => '\1',
                    '/(quiz)zes$/' => '\1',
                    '/(matr)ices$/' => '\1ix',
                    '/(vert|ind)ices$/' => '\1ex',
                    '/^(ox)en/' => '\1',
                    '/(alias|status)(es)?$/' => '\1',
                    '/(octop|vir)(us|i)$/' => '\1us',
                    '/^(a)x[ie]s$/' => '\1xis',
                    '/(cris|test)(is|es)$/' => '\1is',
                    '/(shoe)s$/' => '\1',
                    '/(o)es$/' => '\1',
                    '/(bus)(es)?$/' => '\1',
                    '/^(m|l)ice$/' => '\1ouse',
                    '/(x|ch|ss|sh)es$/' => '\1',
                    '/(m)ovies$/' => '\1ovie',
                    '/(s)eries$/' => '\1eries',
                    '/([^aeiouy]|qu)ies$/' => '\1y',
                    '/([lr])ves$/' => '\1f',
                    '/(tive)s$/' => '\1',
                    '/(hive)s$/' => '\1',
                    '/([^f])ves$/' => '\1fe',
                    '/(^analy)(sis|ses)$/' => '\1sis',
                    '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)(sis|ses)$/' => '\1sis',
                    '/([ti])a$/' => '\1um',
                    '/(n)ews$/' => '\1ews',
                    '/(ss)$/' => '\1',
                    '/s$/' => '',
                ];
                $rules = $singularRules;
            }
            foreach ($rules as $rule => $replacement) {
                if (preg_match($rule, $word)) {
                    $result = preg_replace($rule, $replacement, $word);
                }
            }
        }
        if ($result === null) {
            return $originalWord;
        }
    }

    public static function singularize($word) {
        $result = self::getSpecialResult($word, false);
        if ($result !== null) {
            return $result;
        }
        $result = self::$convert($word, true, $rules);
        if ($result === null) {
            return $word;
        }
    }
}
