# furkanmeclis/php-router
[![Latest Stable Version](http://poser.pugx.org/furkanmeclis/router/v)](https://packagist.org/packages/furkanmeclis/router) [![Total Downloads](http://poser.pugx.org/furkanmeclis/router/downloads)](https://packagist.org/packages/furkanmeclis/router) [![Latest Unstable Version](http://poser.pugx.org/furkanmeclis/router/v/unstable)](https://packagist.org/packages/furkanmeclis/router) [![License](http://poser.pugx.org/furkanmeclis/router/license)](https://packagist.org/packages/furkanmeclis/router)
```
  _____  _    _ _____             _____             _            
 |  __ \| |  | |  __ \           |  __ \           | |           
 | |__) | |__| | |__) |  ______  | |__) |___  _   _| |_ ___ _ __
 |  ___/|  __  |  ___/  |______| |  _  // _ \| | | | __/ _ \ '__|
 | |    | |  | | |               | | \ \ (_) | |_| | ||  __/ |   
 |_|    |_|  |_|_|               |_|  \_\___/ \__,_|\__\___|_|   

```
Php İçin Dil Destekli Yönlendirme Sınıfı.

### Özellikler
- GET,POST,PUT ve DELETE istek metotları destekleniyor.
- Controller dosyaları destekleniyor.
- Middleware kontrolü yapılabiliyor.
- Özelleştirilmiş parametreler destekleniyor.
- Yeni doğrulama deseni eklenebiliyor.
- Namespace desteği mevcut.
- Gruplama özelliği mevcut.
- Dil desteği mevcut.
- Özelleştirilmiş hata sayfaları.

### Yükleme
- Composer İle Yükleme

    ```bash
    composer require furkanmeclis/router
    ```
- Manuel Olarak yükleme

     `src/Router.php` Dosyasını indirerek projenize dahil edebilirsiniz.

### Örnek Kullanım
```php
<?php
    require './vendor/autoload.php';
    $router = new furkanmeclis\Router([
        "namespaces" => [
            "controller" => 'App\Controller\\',
            "middleware" => 'App\Middleware\\'
        ],
        "paths" => [
        "controller" => 'App/Controller/',
        "middleware" => 'App/Middleware/'
        ],
        "error" => [
            "controller" => "Home",
            "method" => "error"
        ],
        "language" => [
            "default_language" => "tr",
            "router_file_url" => "/router.json"
        ]
    ]);

    $router->get('/',function(){
        echo "Welcome Home Page";
    });

    $router->group('/api',function($r){

        $r->get('/home','ApiController@Home');

        $rr->post('/user/:id','ApiController@getUser');

    },'TestMiddleware');

    $router->initLanguage([
        "tr" => [
            "home" => ["anasayfa","Homecontroller@home"],
            "contact" =>["iletisim","Homecontroller@contact"]
        ],
        "en" => [
            "home" => ["home","Homecontroller@home"],
            "contact" =>["contact","Homecontroller@contact"]
        ]
    ]);
    $router->language();
    $router->run();

?>
```
## Dökümantasyon
Dökümantasyon sayfasına [burdan](https://github.com/furkanmeclis/php-router/wiki) ulaşabilirsiniz.
