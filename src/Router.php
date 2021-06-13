<?php
namespace furkanmeclis;
/**
 * Router
 * @author Furkan Meclis <gamefurkanmeclis@gmail.com>
 * @method static init()
 * @method static initLanguage($routearray)
 * @method static get($path,$callback, $middleware = false)
 * @method static post($path,$callback, $middleware = false)
 * @method static put($path,$callback, $middleware = false)
 * @method static delete($path,$callback, $middleware = false)
 * @method static setLanguage($lang)
 * @method static getActiveLanguage()
 * @method static setDefaultLanguage()
 * @method static getLink($link)
 * @method static run()
 * @method static error($errorclassname)
 * @method static setGlobalMiddleware($middleware)
 * @method static group($prefix, \Closure $closure, $middleware = false)
 * @method static language($method = 'get', $middleware = false)
 * @method static where($key, $pattern)
 * @method static redirect($from, $to, $status = 301)
 * @copyright Tüm Hakları Furkan Meclis'e Aittir
 * @Licence: The MIT License (MIT) - Copyright (c) - http://opensource.org/licenses/MIT
 * 
 */
class Router
{
    
    public static $patterns = [
        ':id[0-9]?' => '([0-9]+)',
        ':url[0-9]?' => '([0-9a-zA-Z-_]+)'
    ];
    public static $base_url = '/';
    public static $hasRoute = false;
    public static $routes = [];
    public static $group_middleware = false;
    public static $prefix = '';
    public static $default = "";
    public static $language = '';
    public static $languageoptiondir = '';
    public static $namespaces = [];
    public static $paths = [];
    public static $error = [];    
    /**
     * Sınıfa gerekli olan parametreleri tanımlarsınız
     *
     * @param  mixed $settings Sınıfı başlatırken gerekli olan parametre
     * @param  mixed $autoload Controller ve Middleware için otomatik yükleme methodunu çalıştırır
     * @return void
     */
    public static function init($settings, $autoload = true)
    {
        self::$base_url = $settings["base_url"];
        self::$namespaces["controller"] = $settings["namespaces"]["controller"] ;
        self::$namespaces["middleware"] = $settings["namespaces"]["middleware"] ;
        self::$paths["controller"] = $settings["paths"]["controller"] ;
        self::$paths["middleware"] = $settings["paths"]["middleware"] ;
        self::$error["controller"] = $settings["error"]["controller"];
        self::$error["method"] = $settings["error"]["method"];
        self::$languageoptiondir = $settings["language"]["router_file_url"];
        self::$default = $settings["language"]["default_language"];
        self::$language = $settings["language"]["default_language"];
        if ($autoload == true)
            self::loadFiles();
    }    
    /**
     * initLanguage
     *
     * @param  mixed $routearray Dil sisteminde gerekli olan array
     * @return void
     */
    public static function initLanguage($routearray)
    {
        $data = json_encode($routearray);
        file_put_contents(realpath(".") . self::$languageoptiondir, $data);
        chmod(realpath(".") . self::$languageoptiondir, 0777);
    }    
    /**
     * get
     *
     * @param  mixed $path Tarayıcıya girilen url
     * @param  mixed $callback Eşleşme olduktan sonra çalışan fonksiyon
     * @param  mixed $middleware Middleware kontrolü
     * @return void
     */
    public static function get($path, $callback, $middleware = false)
    {
        if ($middleware == false) {
            if (self::$group_middleware == false) {
                $mid = false;
            } else {
                $mid = self::$group_middleware;
            }
        } else {
            $mid = $middleware;
        }
        self::$routes['get'][self::$prefix . $path] = [
            'callback' => $callback,
            'middleware' => $mid
        ];
        return new self();
    }
    /**
     * post
     *
     * @param  mixed $path Tarayıcıya girilen url
     * @param  mixed $callback Eşleşme olduktan sonra çalışan fonksiyon
     * @param  mixed $middleware Middleware kontrolü
     * @return void
     */
    public static function post($path, $callback, $middleware = false)
    {
        if ($middleware == false) {
            if (self::$group_middleware == false) {
                $mid = false;
            } else {
                $mid = self::$group_middleware;
            }
        } else {
            $mid = $middleware;
        }
        self::$routes['post'][self::$prefix . $path] = [
            'callback' => $callback,
            'middleware' => $mid
        ];
        return new self();
    }
    /**
     * put
     *
     * @param  mixed $path Tarayıcıya girilen url
     * @param  mixed $callback Eşleşme olduktan sonra çalışan fonksiyon
     * @param  mixed $middleware Middleware kontrolü
     * @return void
     */
    public static function put($path, $callback, $middleware = false)
    {
        if ($middleware == false) {
            if (self::$group_middleware == false) {
                $mid = false;
            } else {
                $mid = self::$group_middleware;
            }
        } else {
            $mid = $middleware;
        }
        self::$routes['put'][self::$prefix . $path] = [
            'callback' => $callback,
            'middleware' => $mid
        ];
        return new self();
    }
    /**
     * delete
     *
     * @param  mixed $path Tarayıcıya girilen url
     * @param  mixed $callback Eşleşme olduktan sonra çalışan fonksiyon
     * @param  mixed $middleware Middleware kontrolü
     * @return void
     */
    public static function delete($path, $callback, $middleware = false)
    {
        if ($middleware == false) {
            if (self::$group_middleware == false) {
                $mid = false;
            } else {
                $mid = self::$group_middleware;
            }
        } else {
            $mid = $middleware;
        }
        self::$routes['delete'][self::$prefix . $path] = [
            'callback' => $callback,
            'middleware' => $mid
        ];
        return new self();
    }    
    /**
     * setLanguage
     *
     * @param  mixed $lang Dil kısaltması
     * @return void
     */
    public static function setLanguage($lang)
    {
        self::$language = $lang;

        setcookie("lang", $lang, time() + (60 * 60 * 24 * 30));
        return true;
    }    
    /**
     * getActiveLanguage
     *
     * @return void
     */
    public static function getActiveLanguage()
    {
        if (self::$language != '') {
            $lang = self::$language;
        } elseif (isset($_COOKIE["lang"])) {
            $lang = $_COOKIE["lang"];
        } else {
            self::setDefaultLanguage();
            $lang = self::$default;
        }

        return $lang;
    }    
    /**
     * setDefaultLanguage
     *
     * @return void
     */
    public static function setDefaultLanguage()
    {
        self::$language = self::$default;
        setcookie("lang", self::$default, time() + (60 * 60 * 24 * 30));
    }    
    /**
     * getLink
     *
     * @param  mixed $link Tanımlanan dil sisteminde eşleşen key
     * @return void
     */
    public static function getLink($link)
    {
        $lang = self::getActiveLanguage();


        $dir = realpath(".") . self::$languageoptiondir;
        if (file_exists($dir)) {
            $data = file_get_contents($dir);
            $data = json_decode($data, true);
            return $lang . "/" . $data[$lang][$link][0];
        } else {
            return false;
        }
    }    
    /**
     * Sınıftaki tanımlamaları çalıştırır
     *
     * @return void
     */
    public static function run()
    {
        $url = self::getUrl();
        $method = self::getMethod();
        if (!empty(self::$routes) || isset(self::$routes[$method])) {
            if (isset(self::$routes[$method])) {


                foreach (self::$routes[$method] as $path => $props) {

                    foreach (self::$patterns as $key => $pattern) {
                        $path = preg_replace('#' . $key . '#', $pattern, $path);
                    }
                    $pattern = '#^' . $path . '$#';

                    if (preg_match($pattern, $url, $params)) {

                        self::$hasRoute = true;
                        array_shift($params);

                        if (isset($props['redirect'])) {
                            header("Location:" . self::$base_url, true, $props['status']);
                        } else {
                            if ($props['middleware'] != false) {
                                $response = self::checkMiddleware($props['middleware']);
                            } else {
                                $response = true;
                            }

                            if ($response == false) {
                                exit;
                            }
                            $callback = $props['callback'];

                            if (is_callable($callback)) {
                                call_user_func_array($callback, $params);
                            } elseif (is_string($callback)) {

                                [$controllerName, $methodName] = explode('@', $callback);

                                $controllerName = self::$namespaces["controller"] . $controllerName;
                                if (class_exists($controllerName)) {
                                    $controller = new $controllerName();
                                    if (method_exists($controller, $methodName)) {
                                        call_user_func_array([$controller, $methodName], $params);
                                    } else {
                                        self::$hasRoute = false;
                                        self::routerError("<b>$methodName</b> Method Not Found");
                                    }
                                } else {
                                    self::$hasRoute = false;
                                    self::routerError("<b>$controllerName</b> Controller Not Found");
                                }
                            }
                        }
                    }
                }
                self::routerError("Page Not Found", true);
            }
        } else {
            self::routerError("No Route Defined", true);
        }
    }    
    public static function routerError($text, $displaymethod = false)
    {
        if (self::$hasRoute === false) {

            if ($displaymethod == true) {
                $controllerName = self::$namespaces["controller"] . self::$error["controller"];
                $methodName = self::$error["method"];
                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    if (method_exists($controller, $methodName)) {
                        call_user_func_array([$controller, $methodName], ["1" => $text]);
                    }
                }
            } else {
                die($text);
            }
        }
    }    
    /**
     * error
     *
     * @param  mixed $errorclassname Herhangi bir hatada çalışmasını istediğiniz sınıf ve method
     * @return void
     */
    public static function error($errorclassname)
    {

        [$controllerName, $methodName] = explode('@', $errorclassname);
        $controllerName = self::$namespaces . $controllerName;
        if (class_exists($controllerName)) {
            $controller = new $controllerName();
            if (method_exists($controller, $methodName)) {
                self::$error = [
                    "controller" => $controllerName,
                    "method" => $methodName,

                ];
            } else {
                echo "<b>$methodName</b> Method Not Found";
            }
        } else {
            echo "<b>$controllerName</b> Controller Not Found";
        }
    }
    public static function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
    public static function checkMiddleware($middlewareclassname)
    {

        $namespace = self::$namespaces["middleware"] . $middlewareclassname;
        $mid = new $namespace();
        return $mid->handle();
    }
    public static function getUrl()
    {
        return str_replace(self::$base_url, '', $_SERVER['REQUEST_URI']);
    }
    public static function loadFiles()
    {
        $dir = realpath(".") . "/" . self::$paths["controller"];
        $data = scandir($dir);
        unset($data[0]);
        unset($data[1]);
        foreach ($data as $file => $name) {
            require $dir . "/" . $name;
        }
        $dir2 = realpath(".") . "/" . self::$paths["middleware"];
        $data2 = scandir($dir2);
        unset($data2[0]);
        unset($data2[1]);
        foreach ($data2 as $file => $name) {
            require $dir2 . "/" . $name;
        }
    }    
    /**
     * setGlobalMiddleware
     *
     * @param  mixed $middleware Global middleware sınıf ismi
     * @return void
     */
    public static function setGlobalMiddleware($middleware)
    {
        self::$group_middleware = $middleware;
    }    
    /**
     * group
     *
     * @param  mixed $prefix Gruplamadaki ön ek
     * @param  mixed $closure Tanımlamalar
     * @param  mixed $middleware Çalışmasını istediğiniz middleware
     * @return void
     */
    public static function group($prefix, \Closure $closure, $middleware = false)
    {
        self::$prefix = $prefix;
        if ($middleware != null) {
            self::$group_middleware = $middleware;
        }
        $closure();
        self::$prefix = '';
        self::$group_middleware = false;
    }
    public static function getJsonData()
    {
        $lang = self::getActiveLanguage();
        $dir = realpath(".") . self::$languageoptiondir ;
        $data = file_get_contents($dir);
        $data = json_decode($data, true);
        return isset($data[$lang]) ? $data[$lang] : false;
    }    
    /**
     * language
     *
     * @param  mixed $method İstek yöntemi
     * @param  mixed $middleware
     * @return void
     */
    public static function language($method = 'get', $middleware = false)
    {
        $lang = self::getActiveLanguage();
        if ($middleware != null) {
            self::$group_middleware = $middleware;
        }

        $lang_datas = self::getJsonData();
        if ($lang_datas != false) {


            foreach ($lang_datas as $data => $value) {
                if (!isset($value[2]) || $value[2] == false) {
                    if (self::$group_middleware == false) {
                        $mid = false;
                    } else {
                        $mid = self::$group_middleware;
                    }
                } else {
                    $mid = $value[2];
                }
                self::$routes[$method]["/" . $lang . "/" . $value[0]] = [
                    'callback' => $value[1],
                    'middleware' => $mid
                ];
            }
        }

        self::$group_middleware = false;
        return self::$routes;
    }
        
    /**
     * where
     *
     * @param  mixed $key Yeni pattern keyi
     * @param  mixed $pattern Yeni pattern regexi
     * @return void
     */
    public function where($key, $pattern)
    {
        self::$patterns[':' . $key] = '(' . $pattern . ')';
    }    
    /**
     * redirect
     *
     * @param  mixed $from Url
     * @param  mixed $to Yönlendirmesini istediğiniz url
     * @param  mixed $status Status code
     * @return void
     */
    public static function redirect($from, $to, $status = 301)
    {
        self::$routes['get'][$from] = [
            'redirect' => $to,
            'status' => $status
        ];
    }
}
