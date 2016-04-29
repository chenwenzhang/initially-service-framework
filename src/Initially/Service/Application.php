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
     * @var bool
     */
    private $isBootstrap = false;

    /**
     * @var string
     */
    private $appStage;

    /**
     * @var string
     */
    private $configPath;

    /**
     * Application constructor.
     *
     * @param string $stage
     * @param string $configPath
     * @throws AppException
     */
    public function __construct($stage = null, $configPath = null)
    {
        $this->appStage = $stage;
        $this->configPath = $configPath;
        if (is_null($this->appStage)) {
            throw new AppException("Stage error");
        } elseif (!is_dir($this->configPath)) {
            throw new AppException("Config path error");
        }
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
            $this->isBootstrap = !$this->isBootstrap;
            DependencyInjection::set("config", $this->_initConfig());
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
     * Init config
     * @return \Closure
     */
    private function _initConfig()
    {
        return function(){
            $directories = array(
                $this->configPath . "/common",
                $this->configPath . "/" . $this->appStage
            );
            return Config::factory($directories);
        };
    }

    /**
     * @return string
     */
    public function getAppStage()
    {
        return $this->appStage;
    }

    /**
     * @return string
     */
    public function getConfigPath()
    {
        return $this->configPath;
    }

}