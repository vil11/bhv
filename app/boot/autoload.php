<?php

/**
 * Loads corresponding App Model class if it is called validly.
 * Loads corresponding Integrity Test class if it is called validly.
 * Loads corresponding Dom Lib class if it is called validly.
 */
class autoloader
{
    /**
     * @param string $className
     * @throws Exception
     */
    public static function autoload(string $className)
    {
        $fileRelativeName = 'model' . DS . str_replace('_', DS, $className);
        $path = PATH_APP . $fileRelativeName . '.php';
//        $result = self::require($path);
        if (file_exists($path)) {
            require_once $path;
            $result = true;
        }

        if (!$result) {
            $types = ['integrity'];
            foreach ($types as $type) {
                $fileRelativeName = 'tests' . DS . $type . DS . str_replace('_', DS, $className);
                $path = PATH_QA . $fileRelativeName . '.php';
//                $result = self::require($path);
                if (file_exists($path)) {
                    require_once $path;
                    $result = true;
                }
                if ($result) {
                    break;
                }
            };
        }

        if (!$result) {
            $fileRelativeName = str_replace(
                'Laminas' . DS . 'Dom',
                'laminas' . DS . 'laminas-dom' . DS . 'src',
                $className
            );
            $path = PATH_VENDOR . $fileRelativeName . '.php';
//            $result = self::require($path);
            if (file_exists($path)) {
                require_once $path;
                $result = true;
            }
        }


        if (!$result && $className !== 'PHP_Invoker') {
            $err = err('Class "%s" was not found.', $className);
            $err = prepareIssueCard($err, $path);
            throw new Exception($err);
        }
    }
}
