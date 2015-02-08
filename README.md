# Frankie - A frankenstein micro-framework for PHP

The goal is focus on actions and attach before and action events using
annotations

```php
<?php
class MyController
{
    use EventManager;

    /**
     * @Route("/my/path", methods={"GET"})
     * @Before(targetClass="MyHook\ThisOne", targetMethod="count")
     * @After(targetClass="MyHook\AnotherOne", targetMethod="record")
     */
    public function get(Request $request, Response $response)
    {
        // ...
        $this->getEventManager()->trigger("mark-it", $element);
        // ...
    }
}

```
