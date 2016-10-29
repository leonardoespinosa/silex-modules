<?php

namespace App\Enterprises;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EnterprisesController implements ControllerProviderInterface
{
  public function connect(Application $app)
  {
    $controller = $app['controllers_factory'];

    $controller->get('/enterprise-list', function() use($app) {

      $user = $app['session']->get('user');
      $enterprises = $app['session']->get('enterprises');

      if( !isset( $enterprises ) || empty( $enterprises ) ){
        $enterprises = array(
          array(
            'id' => 1,
            'nit' => '03846463-12',
            'nombre' => 'Google Inc.',
            'direccion' => '1600 Amphitheatre Parkway Mountain View, CA 94043',
            'sitioWeb' => 'www.google.com'
          )
        );
        $app['session']->set('enterprises', $enterprises);
      }

      if ( isset( $user ) && $user != '' ) {
        return $app['twig']->render('Enterprises/enterprises.list.html.twig', array(
          'user' => $user,
          'enterprises' => $enterprises
        ));
      } else {
        return $app->redirect( $app['url_generator']->generate('login'));
      }
    })->bind('enterprise-list');

    $controller->get('/enterprise-edit', function() use($app) {
      $user = $app['session']->get('user');
      if ( isset( $user ) && $user != '' ) {
        return $app['twig']->render('Enterprises/enterprises.edit.html.twig', array(
          'user' => $user
        ));
      } else {
        return $app->redirect( $app['url_generator']->generate('login'));
      }
    })->bind('enterprise-edit');

    $controller->post('/enterprise-save', function( Request $request ) use ( $app ){
      $enterprises = $app['session']->get('enterprises');
      $enterprises[] = array(
        'id' => $request->get('id'),
        'nit' => $request->get('nit'),
        'nombre' => $request->get('nombre'),
        'direccion' => $request->get('direccion'),
        'sitioWeb' => $request->get('sitioWeb')
      );
      $app['session']->set('enterprises', $enterprises);
      return $app->redirect( $app['url_generator']->generate('enterprise-list') );
    })->bind('enterprise-save');

    return $controller;
  }
}