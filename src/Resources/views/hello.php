<?php
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Templating\PhpEngine;
/**
 * @var PhpEngine $view
 * @var PathPackage $JsAssets
 */
?>

<pre>
Hello, <?= $name ?>!

Js file: <?= $JsAssets->getUrl('jquery.js') ?>
</pre>
