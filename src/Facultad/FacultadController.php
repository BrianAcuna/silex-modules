<?php

namespace App\Facultad;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FacultadController implements ControllerProviderInterface
{

  public function connect(Application $app)
  {
    // creates a new controller based on the default route
    $controller = $app['controllers_factory'];

    // la ruta "/users/list"
    $controller->get('/list', function() use($app) {

      // obtiene el nombre de usuario de la sesión
      $user = $app['session']->get('user');

      // obtiene el listado de usuarios
      $use = $app['session']->get('use');
      if (!isset($use)) {
        $use = array();
      }

      // ya ingreso un usuario ?
      if ( isset( $user ) && $user != '' ) {
        // muestra la plantilla
        return $app['twig']->render('Users/users.list.html.twig', array(
          'user' => $user,
          'use' => $use
        ));

      } else {
        // redirige el navegador a "/login"
        return $app->redirect( $app['url_generator']->generate('login'));
      }

    // hace un bind
    })->bind('users-list');

    // la ruta "/users/new"
    $controller->get('/new', function() use($app) {

      // obtiene el nombre de usuario de la sesión
      $user = $app['session']->get('user');

      // ya ingreso un usuario ?
      if ( isset( $user ) && $user != '' ) {
        // muestra la plantilla
        return $app['twig']->render('Users/users.edit.html.twig', array(
          'user' => $user,
          'index' => '',
          'user_to_edit' => array(
              'id' => '',
              'nombre' => '',
              'decano' => '',
              'email' => '',
              'sitio' => ''
            )
        ));

      } else {
        // redirige el navegador a "/login"
        return $app->redirect( $app['url_generator']->generate('login'));
      }

    // hace un bind
    })->bind('users-new');

    // la ruta "/users/edit"
    $controller->get('/edit/{index}', function($index) use($app) {

      // obtiene el nombre de usuario de la sesión
      $user = $app['session']->get('user');

      // obtiene los usuarios de la sesión
      $use = $app['session']->get('use');
      if (!isset($use)) {
        $use = array();
      }

      // no ha ingresado el usuario (no ha hecho login) ?
      if ( !isset( $user ) || $user == '' ) {
        // redirige el navegador a "/login"
        return $app->redirect( $app['url_generator']->generate('login'));

      // no existe un usuario en esa posición ?
      } else if ( !isset($use[$index])) {
        // muestra el formulario de nuevo usuario
        return $app->redirect( $app['url_generator']->generate('users-new') );

      } else {
        // muestra la plantilla
        return $app['twig']->render('Users/users.edit.html.twig', array(
          'user' => $user,
          'index' => $index,
          'user_to_edit' => $use[$index]
        ));

      }

    // hace un bind
    })->bind('users-edit');

    $controller->post('/save', function( Request $request ) use ( $app ){

      // obtiene los usuarios de la sesión
      $use = $app['session']->get('use');
      if (!isset($use)) {
        $use = array();
      }

      // index no está incluido en la petición
      $index = $request->get('index');
      if ( !isset($index) || $index == '' ) {
        // agrega el nuevo usuario
        $use[] = array(
          'id' => $request->get('id'),
          'nombre' => $request->get('nombre'),
          'decano' => $request->get('decano'),
          'email' => $request->get('email'),
          'sitio' => $request->get('sitio')
        );
      } else {
        // modifica el usuario en la posición $index
        $use[$index] = array(
          'id' => $request->get('id'),
          'nombre' => $request->get('nombre'),
          'decano' => $request->get('decano'),
          'email' => $request->get('email'),
          'sitio' => $request->get('sitio')
        );
      }

      // actualiza los datos en sesión
      $app['session']->set('use', $use);

      // muestra la lista de usuarios
      return $app->redirect( $app['url_generator']->generate('users-list') );
    })->bind('users-save');

    $controller->get('/delete/{index}', function($index) use ($app) {

      // obtiene los usuarios de la sesión
      $use = $app['session']->get('use');
      if (!isset($use)) {
        $use = array();
      }

      // no existe un usuario en esa posición ?
      if ( isset($use[$index])) {
        unset ($use[$index]);
        $app['session']->set('use', $use);
      }

      // muestra la lista de usuarios
      return $app->redirect( $app['url_generator']->generate('users-list') );

    })->bind('users-delete');

    return $controller;
  }

}