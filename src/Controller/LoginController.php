<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 23.10.15
 * Time: 9:28
 */

namespace DG\SymfonyCert\Controller;


use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

class LoginController extends Controller
{
    public function loginAction()
    {
        $form = $this->createLoginForm();

        return new Response(
            $this->getTwig()->render('crud/login.html.twig', [
                'form' => $form->createView()
            ])
        );
    }

    public function loginCheckAction()
    {
        //do nothing because security component will start authentication checking
    }

    private function createLoginForm()
    {
        /** @var Translator $translator */
        $translator = $this->get('translator');
        $validator = Validation::createValidatorBuilder()
            ->setTranslator($translator)
            ->setTranslationDomain('validators')
            ->getValidator();

        /** @var CsrfTokenManager $csrfTokenManager */
        $csrfTokenManager = $this->get('csrf_token.manager');

        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->addExtension(new ValidatorExtension($validator))
            ->addExtension(new CsrfExtension($csrfTokenManager, $translator, 'validators'))
            ->getFormFactory();

        $form = $formFactory->createBuilder('form', null, [
            'action' => $this->get('url_generator')->generate('login_check'),
            'csrf_token_id' => 'authenticate'
        ])
            ->add('_username', 'text', [
                'constraints' => [ new NotBlank() ]
            ])
            ->add('_password', 'password', [
                'constraints' => [ new NotBlank() ]
            ])
            ->add('Login', 'submit', [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->getForm()
        ;

        return $form;
    }
} 