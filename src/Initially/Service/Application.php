<?php
namespace Initially\Service;

use Initially\Rpc\Core\Engine\ServerApplication;
use ReflectionClass;

/**
 * Class Application
 *
 * @package Initially\Service
 */
class Application
{

    /**
     * @var Application
     */
    private static $instance;

    /**
     * @var bool
     */
    private $isBootstrap = false;

    /**
     * @var string
     */
    private $appStage;

    /**
     * Application constructor.
     *
     * @throws AppException
     */
    private function __construct()
    {
        if (!defined("CONF_PATH")) {
            throw new AppException("CONF_PATH undefined");
        }
    }

    /**
     * @return Application
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Application();
        }
        return self::$instance;
    }

    /**
     * Bootstrap
     *
     * @param Bootstrap $bootstrap
     * @return $this
     */
    public function bootstrap(Bootstrap $bootstrap)
    {
        if (!$this->isBootstrap) {
            $this->_appStageCheck();
            $this->isBootstrap = !$this->isBootstrap;
            $bootstrapReflection = new ReflectionClass($bootstrap);
            foreach ($bootstrapReflection->getMethods() as $method) {
                if (strpos($method->getName(), "_init") === 0) {
                    $result = $method->invoke($bootstrap);
                    $name = lcfirst(str_replace("_init", "", $method->getName()));
                    DependencyInjection::set($name, $result);
                }
            }
        }
        return $this;
    }

    /**
     * @param string $serviceConfigFile
     */
    public function run($serviceConfigFile)
    {
        $initiallyRpcServerApp = new ServerApplication($serviceConfigFile);
        $initiallyRpcServerApp->run();
    }


    /**
     * @return string
     */
    public function getAppStage()
    {
        return $this->appStage;
    }

    /**
     * @param $appStage
     * @return $this
     */
    public function setAppStage($appStage)
    {
        $this->appStage = $appStage;
        return $this;
    }

    /**
     * @throws AppException
     */
    private function _appStageCheck()
    {
        if (!isset($this->appStage)) {
            throw new AppException("unset app stage, please call Initially\\Service\\Application::setAppStage");
        }
    }

}