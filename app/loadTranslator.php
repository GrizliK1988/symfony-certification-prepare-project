<?php

namespace DG\App;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;

/**
 * @param ContainerInterface $container
 * @return Translator
 */
function loadTranslator(ContainerInterface $container)
{
    $translator = new Translator('ru');
    $translator->addLoader('xlf', new XliffFileLoader());

    $translator->addResource('xlf',
        VENDOR_PATH . 'symfony/form/Resources/translations/validators.ru.xlf',
        'ru',
        'validators');
    $translator->addResource('xlf',
        VENDOR_PATH . 'symfony/validator/Resources/translations/validators.ru.xlf',
        'ru',
        'validators');

    $container->set('translator', $translator);

    return $translator;
}
