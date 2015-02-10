# Frankie - A frankenstein micro-framework for PHP

The goal is focus on actions and attach before and action events using
annotations

```php
<?php
class MyController
{
    /**
     * @Inject
     * @var Zend\EventManager\EventManager
     */
    private $eventManager;

    /**
     * @Route("/my/path", methods={"GET"})
     * @Before(targetClass="MyHook\ThisOne", targetMethod="count")
     */
    public function get(Request $request, Response $response)
    {
        // ...
        $this->eventManager->trigger("mark-it", $element);
        // ...
    }
}

```
