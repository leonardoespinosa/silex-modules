<?php

namespace App\Login;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginController implements ControllerProviderInterface
{

  // la función "connect" define las rutas del módulo
  public function connect(Application $app)
  {
    // creates a nuevo controlador
    $controller = $app['controllers_factory'];

    // Configura las rutas del módulo
    // ==============================

    // la ruta "/login" muestra el formulario de login
    $controller->get('/login', function() use($app) {

      // obtiene el nombre de usuario de la sesión
      $user = $app['session']->get('user');

      // ya ingreso un usuario ?
      if ( isset( $user ) && $user != '' ) {
        // redirige el navegador a "/login"
        return $app->redirect( $app['url_generator']->generate('menu'));
      }

      // muestra la plantilla views/login.html.twig
      // usando como valor $user
      return $app['twig']->render('Login/login.html.twig', array(
        'user' => $user
      ));

      // al usar bind define el nombre 'login'
      // en las plantillas es posible incluir un link usando url('login')
      // es posible hacer un redirect usando $app['url_generator']->generate('login')
    })->bind('login');


    // la ruta "/doLogin" recibe los datos del formulario
    // note que se recibe $request como parámetro
    $controller->post('/doLogin', function(Request $request) use($app) {

      // toma los datos de la petición web
      $login = $request->get('login');
      $password = $request->get('password');

      // coincide el login con el password ?
      if ( $login == $password ) {

        // coloca el nombre del usuario en la sesión
        $app['session']->set('user', $login);
        // redirige el navegador a "/menu"
        return $app->redirect( $app['url_generator']->generate('menu'));

      // no conincide ?
      } else {

        // redirige el navegador a "/login"
        return $app->redirect( $app['url_generator']->generate('login'));
      }

      // hace un bind con el nombre "doLogin"
    })->bind('doLogin');

    // hace un logout
    $controller->get('/doLogout', function() use($app){

      // remueve el usuario de la sesión
      $app['session']->set('user', '');
      $app['session']->set('users', array());            
      $app['session']->set('enterprises', array());

      // redirige el navegador a "/login"
      return $app->redirect( $app['url_generator']->generate('login'));

    })->bind('doLogout');


    // la ruta "/menu" muestra un menú
    $controller->get('/menu', function() use($app) {

      // obtiene el nombre de usuario de la sesión
      $user = $app['session']->get('user');

      // ya ingreso un usuario ?
      if ( isset( $user ) && $user != '' ) {
        // muestra la plantilla views/menu.html.twig
        return $app['twig']->render('inicio.html.twig', array(
          'user' => $user
        ));

      } else {
        // redirige el navegador a "/login"
        return $app->redirect( $app['url_generator']->generate('login'));
      }

      // hace un bind con el nombre "menu"
    })->bind('menu');

    // retorna el controlador
       return $controller;
  }
}
