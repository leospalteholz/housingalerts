<?php

namespace Tests\Feature\Routing;

use Illuminate\Support\Facades\Route;
use ReflectionClass;
use Tests\TestCase;

class RouteIntegrityTest extends TestCase
{
    public function test_all_controller_routes_reference_existing_classes_and_methods(): void
    {
        foreach (Route::getRoutes() as $route) {
            $controller = $route->getAction('controller');

            if (! is_string($controller) || ! str_contains($controller, '@')) {
                continue;
            }

            [$controllerClass, $method] = explode('@', $controller, 2);

            $this->assertTrue(
                class_exists($controllerClass),
                sprintf('Route [%s] references missing controller class [%s].', $route->uri(), $controllerClass)
            );

            if (! class_exists($controllerClass)) {
                continue;
            }

            $this->assertTrue(
                (new ReflectionClass($controllerClass))->hasMethod($method),
                sprintf('Route [%s] references missing method [%s::%s].', $route->uri(), $controllerClass, $method)
            );
        }
    }

    public function test_named_routes_are_unique(): void
    {
        $seen = [];

        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();

            if (! $name) {
                continue;
            }

            $this->assertArrayNotHasKey(
                $name,
                $seen,
                sprintf('Duplicate route name detected: [%s].', $name)
            );

            $seen[$name] = $route->uri();
        }
    }
}
