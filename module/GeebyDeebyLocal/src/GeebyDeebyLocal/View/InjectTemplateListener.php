<?php
namespace GeebyDeebyLocal\View;

class InjectTemplateListener extends \Zend\Mvc\View\Http\InjectTemplateListener
{
    /**
     * Determine the top-level namespace of the controller
     *
     * @param  string $controller
     * @return string
     */
    protected function deriveModuleNamespace($controller)
    {
        if (is_callable([$controller, 'getModuleTemplateNamespace'])) {
            return $controller::getModuleTemplateNamespace();
        }
        return parent::deriveModuleNamespace($controller);
    }
}
