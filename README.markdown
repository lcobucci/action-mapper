ActionMapper
============

ActionMapper is an open source front-controller framework for PHP 5.

It's based on Struts framework, and the basic features are:

- Basic action map;
- Filters map;
- Automatic action mapper (classes that implements AppAction interface and has the "ActionController")
- Error handler (HTTP 404, HTTP 403 and HTTP 500)

Installation
------------

To install ActionMapper on your project, create a new empty project and clone the repository.

On your project add ActionMapper's folder to your include path:

    $actionMapperRootDir = 'Your path to ActionMapper`s main dir';
    set_include_path(get_include_path() . PATH_SEPARATOR . $actionMapperRootDir);

On your project include the ActionMapper's autoloader and register it with SPL:

    require 'application/br.com.lcobucci.action-mapper/autoloader/ActionMapperAutoLoader.php';
    
    $actionMapperLoader = new ActionMapperAutoLoader();
    $actionMapperLoader->register();

On your project you have to have a .htaccess file like this (to redirect all URI's to a unique file):

    RewriteEngine On
    RewriteRule !\.(js|ico|txt|gif|jpg|jpeg|png|css|pdf|swf)$ index.php
    
Basic Usage
-----------

Create a class that implements the AppAction interface:

    class TestAction implements AppAction
    {
        public function process(AppRequest $request)
        {
            echo 'Hello World';
        }
    }

Map it to an application:

    $app = WebApplication::getInstance();
    $app->attachAction('*', new TestAction());
    $app->run();

Now all your project's URI will use thhe TestAction::process, and will display **Hello World**