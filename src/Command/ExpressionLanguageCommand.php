<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 04.10.15
 * Time: 21:52
 */

namespace DG\SymfonyCert\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SerializedParsedExpression;

class TestClass
{
    public $aa;

    public function hi()
    {
        return 'Hi!';
    }
}

class StringExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @return ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        return [
            new ExpressionFunction('lowercase', function ($str) {
                return sprintf('strtolower(%1$s)', $str);
            }, function ($arguments, $str) {
                return strtolower($str);
            })
        ];
    }
}

class ExpressionLanguageCommand extends Command
{
    protected function configure()
    {
        $this->setName('expression:language:test');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $expressionLanguage = new ExpressionLanguage();
        $expressionLanguage->registerProvider(new StringExpressionLanguageProvider());

        var_dump($expressionLanguage->evaluate('1+2'));

        $compiled = $expressionLanguage->compile('"TEST"~" "~"aaa"');
        var_dump($compiled);

        $testClass = new TestClass();
        $testClass->aa = 123;

        var_dump($expressionLanguage->evaluate('test.aa~" "~test.hi()', [
            'test' => $testClass
        ]));

        $language = new ExpressionLanguage(null, [
            new StringExpressionLanguageProvider()
        ]);
        var_dump($language->evaluate('lowercase("AAA")'));
        eval('var_dump(' . $language->compile('lowercase("AAA")') . ');');

        $expr = new Expression('(1+2)*test.aa');
        $parsedExpression = $language->parse($expr, ['test']);

        var_dump($parsedExpression);
        var_dump($language->evaluate($parsedExpression, ['test' => $testClass]));

        $serializedExpression = new SerializedParsedExpression('(1+2)*test.aa', serialize($parsedExpression->getNodes()));
        var_dump($language->evaluate($serializedExpression, ['test' => $testClass]));
    }
}