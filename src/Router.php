<?php
namespace furkanmeclis;
/**
 * Router
 * @author Furkan Meclis <gamefurkanmeclis@gmail.com>
 * @method  init()
 * @method  initLanguage($routearray)
 * @method  get($path,$callback, $middleware = false)
 * @method  post($path,$callback, $middleware = false)
 * @method  put($path,$callback, $middleware = false)
 * @method  delete($path,$callback, $middleware = false)
 * @method  setLanguage($lang)
 * @method  getActiveLanguage()
 * @method  setDefaultLanguage()
 * @method  getLink($link)
 * @method  run()
 * @method  error($errorclassname)
 * @method  setGlobalMiddleware($middleware)
 * @method  group($prefix, \Closure $closure, $middleware = false)
 * @method  language($method = 'get', $middleware = false)
 * @method  where($key, $pattern)
 * @copyright Tüm Hakları Furkan Meclis'e Aittir
 * @Licence: The MIT License (MIT) - Copyright (c) - http://opensource.org/licenses/MIT
 * 
 */
class Router
{
    
    public  $patterns =  [
        ':all[0-9]?' => '(.*)',
        ':any[0-9]?' => '([^/]+)',
        ':id[0-9]?' => '(\d+)',
        ':int[0-9]?' => '(\d+)',
        ':number[0-9]?' => '([+-]?([0-9]*[.])?[0-9]+)',
        ':float[0-9]?' => '([+-]?([0-9]*[.])?[0-9]+)',
        ':bool[0-9]?' => '(true|false|1|0)',
        ':string[0-9]?' => '([\w\-_]+)',
        ':slug[0-9]?' => '([\w\-_]+)',
        ':uuid[0-9]?' => '([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})',
        ':date[0-9]?' => '([0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]))',
    ];
   
    public  $hasRoute = false;
    public  $routes = [];
    public  $group_middleware = false;
    public  $prefix = '';
    public  $default = "";
    public  $language = '';
    public  $languageoptiondir = '';
    public  $namespaces = [];
    public  $paths = [];
    public  $error = [];    
    public  $global_middleware = false;
    /**
     * Sınıfa gerekli olan parametreleri tanımlarsınız
     *
     * @param  mixed $settings Sınıfı başlatırken gerekli olan parametre
     * @param  mixed $autoload Controller ve Middleware için otomatik yükleme methodunu çalıştırır
     * @return void
     */
    public  function __construct($settings, $autoload = true)
    {
        $this->namespaces["controller"] = $settings["namespaces"]["controller"] ;
        $this->namespaces["middleware"] = $settings["namespaces"]["middleware"] ;
        $this->paths["controller"] = $settings["paths"]["controller"] ;
        $this->paths["middleware"] = $settings["paths"]["middleware"] ;
        $this->languageoptiondir = $settings["language"]["router_file_url"];
        $this->default = $settings["language"]["default_language"];
        $this->language = $settings["language"]["default_language"];
        if ($autoload == true)
            $this->loadFiles();
    }    
    /**
     * initLanguage
     *
     * @param  mixed $routearray Dil sisteminde gerekli olan array
     * @return void
     */
    public  function initLanguage($routearray)
    {
        $data = json_encode($routearray);
        file_put_contents(realpath(".") . $this->languageoptiondir, $data);
        chmod(realpath(".") . $this->languageoptiondir, 0777);
    }    
    /**
     * get
     *
     * @param  mixed $path Tarayıcıya girilen url
     * @param  mixed $callback Eşleşme olduktan sonra çalışan fonksiyon
     * @param  mixed $middleware Middleware kontrolü
     * @return void
     */
    public  function get($path, $callback, $middleware = false)
    {
        if ($middleware == false) {
            if ($this->group_middleware == false) {
                $mid = false;
            } else {
                $mid = $this->group_middleware;
            }
        } else {
            $mid = $middleware;
        }
        $this->routes['get'][$this->prefix . $path] = [
            'callback' => $callback,
            'middleware' => $mid
        ];
        return $this;
    }
    /**
     * post
     *
     * @param  mixed $path Tarayıcıya girilen url
     * @param  mixed $callback Eşleşme olduktan sonra çalışan fonksiyon
     * @param  mixed $middleware Middleware kontrolü
     * @return void
     */
    public  function post($path, $callback, $middleware = false)
    {
        if ($middleware == false) {
            if ($this->group_middleware == false) {
                $mid = false;
            } else {
                $mid = $this->group_middleware;
            }
        } else {
            $mid = $middleware;
        }
        $this->routes['post'][$this->prefix . $path] = [
            'callback' => $callback,
            'middleware' => $mid
        ];
        return $this;
    }
    /**
     * put
     *
     * @param  mixed $path Tarayıcıya girilen url
     * @param  mixed $callback Eşleşme olduktan sonra çalışan fonksiyon
     * @param  mixed $middleware Middleware kontrolü
     * @return void
     */
    public  function put($path, $callback, $middleware = false)
    {
        if ($middleware == false) {
            if ($this->group_middleware == false) {
                $mid = false;
            } else {
                $mid = $this->group_middleware;
            }
        } else {
            $mid = $middleware;
        }
        $this->routes['put'][$this->prefix . $path] = [
            'callback' => $callback,
            'middleware' => $mid
        ];
        return $this;
    }
    /**
     * delete
     *
     * @param  mixed $path Tarayıcıya girilen url
     * @param  mixed $callback Eşleşme olduktan sonra çalışan fonksiyon
     * @param  mixed $middleware Middleware kontrolü
     * @return void
     */
    public  function delete($path, $callback, $middleware = false)
    {
        if ($middleware == false) {
            if ($this->group_middleware == false) {
                $mid = false;
            } else {
                $mid = $this->group_middleware;
            }
        } else {
            $mid = $middleware;
        }
        $this->routes['delete'][$this->prefix . $path] = [
            'callback' => $callback,
            'middleware' => $mid
        ];
        return $this;
    }    
    /**
     * setLanguage
     *
     * @param  mixed $lang Dil kısaltması
     * @return void
     */
    public  function setLanguage($lang)
    {
        $this->language = $lang;

        setcookie("lang", $lang, time() + (60 * 60 * 24 * 30));
        return true;
    }    
    /**
     * getActiveLanguage
     *
     * @return void
     */
    public  function getActiveLanguage()
    {
        if ($this->language != '') {
            $lang = $this->language;
        } elseif (isset($_COOKIE["lang"])) {
            $lang = $_COOKIE["lang"];
        } else {
            $this->setDefaultLanguage();
            $lang = $this->default;
        }

        return $lang;
    }    
    /**
     * setDefaultLanguage
     *
     * @return void
     */
    public  function setDefaultLanguage()
    {
        $this->language = $this->default;
        setcookie("lang", $this->default, time() + (60 * 60 * 24 * 30));
    }    
    /**
     * getLink
     *
     * @param  mixed $link Tanımlanan dil sisteminde eşleşen key
     * @return void
     */
    public  function getLink($link)
    {
        $lang = $this->getActiveLanguage();


        $dir = realpath(".") . $this->languageoptiondir;
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
    public  function run()
    {
        $url = $this->getUrl();
        $method = $this->getMethod();
        $glabal_middleware = $this->global_middleware;
        $mide = true;
        if($glabal_middleware != false){
            $mide = $this->checkMiddleware($glabal_middleware);
        }
        if($mide == true){
            if (!empty($this->routes) || isset($this->routes[$method])) {
                if (isset($this->routes[$method])) {
    
    
                    foreach ($this->routes[$method] as $path => $props) {
    
                        foreach ($this->patterns as $key => $pattern) {
                            $path = preg_replace('#' . $key . '#', $pattern, $path);
                        }
                        $pattern = '#^' . $path . '$#';
    
                        if (preg_match($pattern, $url, $params)) {
    
                            $this->hasRoute = true;
                            array_shift($params);
    
                           
                                if ($props['middleware'] != false) {
                                    $response = $this->checkMiddleware($props['middleware']);
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
    
                                    $controllerName = $this->namespaces["controller"] . $controllerName;
                                    if (class_exists($controllerName)) {
                                        $controller = new $controllerName();
                                        if (method_exists($controller, $methodName)) {
                                            call_user_func_array([$controller, $methodName], $params);
                                        } else {
                                            $this->hasRoute = false;
                                            $this->routerError("<b>$methodName</b> Method Not Found");
                                        }
                                    } else {
                                        $this->hasRoute = false;
                                        $this->routerError("<b>$controllerName</b> Controller Not Found");
                                    }
                                }
                            
                        }
                    }
                    $this->routerError("Page Not Found", true);
                }
            } else {
                $this->routerError("No Route Defined", true);
            }
        }
    }    
    public  function routerError($text, $displaymethod = false)
    {
        if ($this->hasRoute === false) {

            if (!empty($this->error)) {
                $controllerName = $this->namespaces["controller"] . $this->error["controller"];
                $methodName = $this->error["method"];
                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    if (method_exists($controller, $methodName)) {
                        call_user_func_array([$controller, $methodName], ["1" => $text]);
                    }
                }else{
                    echo "tets";
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
    public  function error($errorclassname)
    {

        $c = explode('@', $errorclassname);
       
        $methodName = $c[1];
        $controllerName = $this->namespaces["controller"] . $c[0];
        if (class_exists($controllerName)) {
            $controller = new $controllerName();
            if (method_exists($controller, $methodName)) {
                $this->error = [
                    "controller" => $c[0],
                    "method" => $methodName,

                ];
            } else {
                echo "<b>$methodName</b> Method Not Found";
            }
        } else {
            echo "<b>$controllerName</b> Controller Not Found";
        }
    }
    public  function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
    public  function checkMiddleware($middlewareclassname)
    {

        $namespace = $this->namespaces["middleware"] . $middlewareclassname;
        $mid = new $namespace();
        return $mid->handle();
    }
    public  function getUrl()
    {
        return isset($_GET["uri"]) ? $_GET["uri"] : '/';
    }
    public  function loadFiles()
    {
        $dir = realpath(".") . "/" . $this->paths["controller"];
        $data = scandir($dir);
        unset($data[0]);
        unset($data[1]);
        foreach ($data as $file => $name) {
            require $dir . "/" . $name;
        }
        $dir2 = realpath(".") . "/" . $this->paths["middleware"];
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
    public  function setGlobalMiddleware($middleware)
    {
        $this->global_middleware = $middleware;
    }    
    /**
     * group
     *
     * @param  mixed $prefix Gruplamadaki ön ek
     * @param  mixed $closure Tanımlamalar
     * @param  mixed $middleware Çalışmasını istediğiniz middleware
     * @return void
     */
    public  function group($prefix, \Closure $callback, $middleware = false)
    {
        $this->prefix = $prefix;
        if ($middleware != null) {
            $this->group_middleware = $middleware;
        }
        if (is_object($callback)) {
            call_user_func_array($callback, [$this]);
        }
        $this->prefix = '';
        $this->group_middleware = false;
    }
    public  function getJsonData()
    {
        $lang = $this->getActiveLanguage();
        $dir = realpath(".") . $this->languageoptiondir ;
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
    public  function language($method = 'get', $middleware = false)
    {
        $lang = $this->getActiveLanguage();
        if ($middleware != null) {
            $this->group_middleware = $middleware;
        }

        $lang_datas = $this->getJsonData();
        if ($lang_datas != false) {


            foreach ($lang_datas as $data => $value) {
                if (!isset($value[2]) || $value[2] == false) {
                    if ($this->group_middleware == false) {
                        $mid = false;
                    } else {
                        $mid = $this->group_middleware;
                    }
                } else {
                    $mid = $value[2];
                }
                $this->routes[$method]["/" . $lang . "/" . $value[0]] = [
                    'callback' => $value[1],
                    'middleware' => $mid
                ];
            }
        }

        $this->group_middleware = false;
        return $this->routes;
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
        $this->patterns[':' . $key] = '(' . $pattern . ')';
    }    
}
