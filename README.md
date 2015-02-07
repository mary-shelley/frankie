# Frankie - A frankenstein micro-framework for PHP

The goal is focus on actions and attach before and action events using
annotations

```php
<?php
class MyController
{
    use EventManager, Dic;

    /**
     * @Route("/my/path", methods={"GET"})
     * @Before(targetClass="MyHook\ThisOne", targetMethod="count")
     * @After(targetClass="MyHook\AnotherOne", targetMethod="record")
     */
    public function get(Bag $bag, Request $request, Response $response)
    {
        $odm = $this->getContainer()->get("mongodb_odm");

        // ...

        $this->getEventManager()->trigger("mark-it", $element);

        // ...
    }
}

```
