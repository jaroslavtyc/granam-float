<?php
namespace Granam\Tests\Float\Tools;

use Granam\Float\FloatObject;
use Granam\Number\NumberObject;
use Granam\Tests\ExceptionsHierarchy\Exceptions\AbstractExceptionsHierarchyTest;

class FloatExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    /**
     * @return string
     */
    protected function getTestedNamespace()
    {
        return str_replace('\Tests', '', __NAMESPACE__);
    }

    /**
     * @return string
     */
    protected function getRootNamespace()
    {
        $rootClassReflection = new \ReflectionClass(FloatObject::class);

        return $rootClassReflection->getNamespaceName();
    }

    /**
     * @return string
     */
    protected function getExternalRootNamespaces()
    {
        $numberClassReflection = new \ReflectionClass(NumberObject::class);

        return $numberClassReflection->getNamespaceName();
    }

}