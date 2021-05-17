<?php

require_once(__DIR__ . '/HTMLPurifier.standalone.php');

/**
 * Class purifier
 *
 * 富文本过滤器
 *
 * 封装 http://htmlpurifier.org/docs
 */

class Purifier
{
    /**
     * @var config
     */
    protected $config;

    /**
     * @var HTMLPurifier
     */
    protected $purifier;

    /**
     * Constructor
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->config = require(__DIR__ . '/config/purifier.php');
        $this->setUp();
    }

    /**
     * @param $key
     * @param null $default
     * @return null
     */
    private function config($key, $default = null) {
        $pathes = explode('.', $key);

        $cur_config = $this->config;
        foreach ($pathes as $path) {
            if (!isset($cur_config[$path])) {
                return $default;
            }
            $cur_config = $cur_config[$path];
        }
        return $cur_config;
    }

    /**
     * Setup
     *
     * @throws Exception
     */
    private function setUp()
    {
        // Create a new configuration object
        $config = HTMLPurifier_Config::createDefault();

        // 基础配置
        $config->loadArray($this->getConfig());

        // 订制配置
        $definition = $this->config('settings.custom_definition');
        if (!empty($definition)) {
            $config->set('HTML.DefinitionID', $definition['id']);
            $config->set('HTML.DefinitionRev', $definition['rev']);
            // Enable debug mode
            if (!isset($definition['debug']) || $definition['debug']) {
                $config->set('Cache.DefinitionImpl', null);
            }
            // 优先加载缓存的配置，如果不存在就执行订制代码
            if ($def = $config->maybeGetRawHTMLDefinition()) {
                $this->addCustomDefinition($definition, $def);
            }
        }

        // Create HTMLPurifier object
        $this->purifier = new HTMLPurifier($this->configure($config));
    }

    /**
     * Add a custom definition
     *
     * @see http://htmlpurifier.org/docs/enduser-customize.html
     * @param array $definitionConfig
     * @param HTML_Purifier_Config $configObject Defaults to using default config
     *
     * @return HTML_Purifier_Config $configObject
     */
    private function addCustomDefinition(array $definitionConfig, $defObj = null)
    {
        // Create the definition attributes
        if (!empty($definitionConfig['attributes'])) {
            $this->addCustomAttributes($definitionConfig['attributes'], $defObj);
        }

        // Create the definition elements
        if (!empty($definitionConfig['elements'])) {
            $this->addCustomElements($definitionConfig['elements'], $defObj);
        }
    }

    /**
     * Add provided attributes to the provided definition
     *
     * @param array $attributes
     * @param HTMLPurifier_HTMLDefinition $definition
     *
     * @return HTMLPurifier_HTMLDefinition $definition
     */
    private function addCustomAttributes(array $attributes, $definition)
    {
        foreach ($attributes as $attribute) {
            // Get configuration of attribute
            $required = !empty($attribute[3]) ? true : false;
            $onElement = $attribute[0];
            $attrName = $required ? $attribute[1] . '*' : $attribute[1];
            $validValues = $attribute[2];

            $definition->addAttribute($onElement, $attrName, $validValues);
        }

        return $definition;
    }

    /**
     * Add provided elements to the provided definition
     *
     * @param array $elements
     * @param HTMLPurifier_HTMLDefinition $definition
     *
     * @return HTMLPurifier_HTMLDefinition $definition
     */
    private function addCustomElements(array $elements, $definition)
    {
        foreach ($elements as $element) {
            // Get configuration of element
            $name = $element[0];
            $contentSet = $element[1];
            $allowedChildren = $element[2];
            $attributeCollection = $element[3];
            $attributes = isset($element[4]) ? $element[4] : null;

            if (!empty($attributes)) {
                $definition->addElement($name, $contentSet, $allowedChildren, $attributeCollection, $attributes);
            } else {
                $definition->addElement($name, $contentSet, $allowedChildren, $attributeCollection);
            }
        }
    }

    /**
     * @param HTMLPurifier_Config $config
     *
     * @return HTMLPurifier_Config
     */
    protected function configure(HTMLPurifier_Config $config)
    {
        return $config;
    }

    /**
     * @return array|null
     */
    protected function getConfig()
    {
        $default_config = [];
        $default_config['Core.Encoding'] = $this->config('encoding');
        $default_config['Cache.SerializerPermissions'] = $this->config('cacheFileMode', 0755);

        $config = $this->config('settings.default');

        if (!is_array($config)) {
            $config = [];
        }

        $config = $default_config + $config;

        return $config;
    }

    /**
     * @param      $dirty
     *
     * @return mixed
     */
    public function purify($dirty)
    {
        return $this->purifier->purify($dirty);
    }
}