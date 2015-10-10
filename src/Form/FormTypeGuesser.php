<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 10.10.15
 * Time: 10:40
 */

namespace DG\SymfonyCert\Form;


use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess;

class FormTypeGuesser implements FormTypeGuesserInterface
{
    /**
     * Returns a field guess for a property name of a class.
     *
     * @param string $class The fully qualified class name
     * @param string $property The name of the property to guess for
     *
     * @return Guess\TypeGuess|null A guess for the field's type and options
     */
    public function guessType($class, $property)
    {
        switch ($property) {
            case 'username':
                return new Guess\TypeGuess('text', [], Guess\Guess::HIGH_CONFIDENCE);
            case 'dob':
                return new Guess\TypeGuess('birthday', [], Guess\Guess::VERY_HIGH_CONFIDENCE);
            case 'age':
                return new Guess\TypeGuess('integer', [], Guess\Guess::VERY_HIGH_CONFIDENCE);
            case 'active':
                return new Guess\TypeGuess('checkbox', [], Guess\Guess::VERY_HIGH_CONFIDENCE);
            case 'photo':
                return new Guess\TypeGuess('file', [], Guess\Guess::HIGH_CONFIDENCE);
            case 'email':
                return new Guess\TypeGuess('email', [], Guess\Guess::VERY_HIGH_CONFIDENCE);

            default:
                return new Guess\TypeGuess('text', [], Guess\Guess::HIGH_CONFIDENCE);
        }
    }

    /**
     * Returns a guess whether a property of a class is required.
     *
     * @param string $class The fully qualified class name
     * @param string $property The name of the property to guess for
     *
     * @return Guess\ValueGuess A guess for the field's required setting
     */
    public function guessRequired($class, $property)
    {
        switch ($property) {
            case 'username':
                return new Guess\ValueGuess(true, Guess\Guess::HIGH_CONFIDENCE);
            case 'dob':
                return new Guess\ValueGuess(false, Guess\Guess::HIGH_CONFIDENCE);
            case 'age':
                return new Guess\ValueGuess(false, Guess\Guess::HIGH_CONFIDENCE);
            case 'active':
                return new Guess\ValueGuess(false, Guess\Guess::HIGH_CONFIDENCE);
            case 'photo':
                return new Guess\ValueGuess(false, Guess\Guess::HIGH_CONFIDENCE);
            case 'email':
                return new Guess\ValueGuess(true, Guess\Guess::HIGH_CONFIDENCE);

            default:
                return new Guess\ValueGuess(false, Guess\Guess::HIGH_CONFIDENCE);
        }
    }

    /**
     * Returns a guess about the field's maximum length.
     *
     * @param string $class The fully qualified class name
     * @param string $property The name of the property to guess for
     *
     * @return Guess\ValueGuess|null A guess for the field's maximum length
     */
    public function guessMaxLength($class, $property)
    {
        return null;
    }

    /**
     * Returns a guess about the field's pattern.
     *
     * - When you have a min value, you guess a min length of this min (LOW_CONFIDENCE) , lines below
     * - If this value is a float type, this is wrong so you guess null with MEDIUM_CONFIDENCE to override the previous guess.
     * Example:
     *  You want a float greater than 5, 4.512313 is not valid but length(4.512314) > length(5)
     *
     * @link https://github.com/symfony/symfony/pull/3927
     *
     * @param string $class The fully qualified class name
     * @param string $property The name of the property to guess for
     *
     * @return Guess\ValueGuess|null A guess for the field's required pattern
     */
    public function guessPattern($class, $property)
    {
        if ($property == 'username') {
            return new Guess\ValueGuess('[A-Z].+', Guess\Guess::HIGH_CONFIDENCE);
        } else {
            return null;
        }
    }

} 