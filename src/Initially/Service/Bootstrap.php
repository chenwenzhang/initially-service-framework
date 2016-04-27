<?php
namespace Initially\Service;

abstract class Bootstrap
{

    /**
     * Init config
     */
    public function _initConfig()
    {
        return function(){
            $directories = array(
                CONF_PATH . "/common",
                CONF_PATH . "/" . Application::getInstance()->getAppStage()
            );
            return Config::factory($directories);
        };
    }

}