<?php

namespace Migrations;


class Module
{

  public function onBootstrap()
  {

  }

  public function getConfig()
  {
    return include __DIR__ . '/../../config/module.config.php';
  }


  public function getAutoloaderConfig()
  {
    return array(
      'Zend\Loader\StandardAutoloader' => array(
        'namespaces' => array(
          __NAMESPACE__ => __DIR__ ,
        ),
      ),
    );
  }
}
