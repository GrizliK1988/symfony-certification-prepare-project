<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 30.10.15
 * Time: 21:03
 */

namespace DG\SymfonyCert\Service;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;

class TranslationFactory
{
    /**
     * @param Request $request
     * @return Translator
     */
    public static function createTranslator(Request $request)
    {
        $translator = new Translator($request->getSession()->get('locale'), new MessageSelector());

        $translator->setFallbackLocales(['ru']);

        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', [
            'username' => 'Имя пользователя',
            'date_of_birth' => 'Дата рождения',
            'photo' => 'Фото',
        ], 'ru', 'crud_view');
        $translator->addResource('array', [
            'username' => 'Username',
            'date_of_birth' => 'Date of birth',
            'photo' => 'Photo',
        ], 'en', 'crud_view');

        $translator->addResource('array', [
            'hello_user' => 'Привет, %username%!',
        ], 'ru', 'auth');
        $translator->addResource('array', [
            'hello_user' => 'Hello, %username%!',
        ], 'en', 'auth');

        $translator->addResource('array', [
            'plural' => '%count% яблоко|%count% яблока|%count% яблок'
        ], 'ru');
        $translator->addResource('array', [
            'plural' => 'One apple|%count% apples'
        ], 'en');

        return $translator;
    }
} 