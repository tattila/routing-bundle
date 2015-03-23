<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\RoutingBundle\Validator\Constraints;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RouteDefaultsValidator extends ConstraintValidator
{
    private $controllerResolver;
    private $templating;

    public function __construct(ControllerResolverInterface $controllerResolver, EngineInterface $templating)
    {
        $this->controllerResolver = $controllerResolver;
        $this->templating = $templating;
    }

    public function validate($defaults, Constraint $constraint)
    {
        if (isset($defaults['_controller']) && null !== $defaults['_controller']) {
            $controller = $defaults['_controller'];

            $request = new Request(array(), array(), array('_controller' => $controller));

            try {
                $this->controllerResolver->getController($request);
            } catch (\LogicException $e) {
                $this->context->addViolation($e->getMessage());
            }
        }

        if (isset($defaults['_template']) && null !== $defaults['_template']) {
            $template = $defaults['_template'];

            if (false === $this->templating->exists($template)) {
                $this->context->addViolation($constraint->message, array('%name%' => $template));
            }
        }
    }
}
