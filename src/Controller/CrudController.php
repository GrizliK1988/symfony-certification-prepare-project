<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 07.10.15
 * Time: 21:51
 */

namespace DG\SymfonyCert\Controller;


use DG\SymfonyCert\Form\FormTypeGuesser;
use DG\SymfonyCert\Form\UserDataClass;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

class CrudController extends Controller
{
    public function viewAction()
    {
        $finder = new Finder();
        $userFiles = $finder->in(STORAGE_PATH)->name('*.json')->sortByModifiedTime()->files();

        $users = function () use ($userFiles) {
            foreach ($userFiles as $userFile) {
                /** @var SplFileInfo $userFile */
                $data = json_decode($userFile->getContents(), true);
                $data['dob'] = new \DateTime($data['dob']['date']);
                $data['id'] = $userFile->getBasename('.json');

                yield $data;
            }
        };

        $response = new Response();
        $response->setContent($this->getTwig()->render('crud/view.html.twig', [
            'users' => $users()
        ]));
        return $response;
    }

    public function addAction(Request $request)
    {
        $form = $this->createForm('add', new UserDataClass());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                /** @var UploadedFile $photo */
                $photo = $form->getData()->photo;
                $data = $form->getData();
                if ($photo) {
                    $photo->move(IMAGES_PATH, $photo->getClientOriginalName());
                    $data->photo = $photo->getClientOriginalName();
                }

                $fs = new Filesystem();
                $fs->dumpFile(STORAGE_PATH . uniqid() . '.json', json_encode($data, JSON_PRETTY_PRINT));

                return new RedirectResponse('/app.php/crud/view');
            }
        }

        $response = new Response();
        $response->setContent($this->getTwig()->render('crud/add.html.twig', [
            'form' => $form->createView()
        ]));
        return $response;
    }

    public function editAction(Request $request)
    {
        $id = $request->query->get('id');
        $userJson = file_get_contents(STORAGE_PATH . $id . '.json');
        $data = json_decode($userJson, true);

        $userClass = new UserDataClass();
        $userClass->username = $data['username'];
        $userClass->email = $data['email'];
        $userClass->dob = new \DateTime($data['dob']['date']);
        $userClass->active = $data['active'];
        $userClass->age = isset($data['age']) ? $data['age'] : 0;
        $userClass->id = $id;

        $form = $this->createForm('edit?id=' . $id, $userClass);
        if ($request->isMethod('POST') && $form->handleRequest($request) && $form->isValid()) {
            $data = $form->getData();
            if ($data->photo) {
                $data->photo->move(IMAGES_PATH, $data->photo->getClientOriginalName());
                $data->photo = $data->photo->getClientOriginalName();
            }

            $prevData = json_decode(file_get_contents(STORAGE_PATH . $id . '.json'), true);
            if ($prevData['photo'] && empty($data->photo)) {
                $data->photo = $prevData['photo'];
            }

            $fs = new Filesystem();
            $fs->dumpFile(STORAGE_PATH . $id . '.json', json_encode($data, JSON_PRETTY_PRINT));

            return new RedirectResponse('/app.php/crud/view');
        }

        $response = new Response();
        $response->setContent($this->getTwig()->render('crud/add.html.twig', [
            'form' => $form->createView()
        ]));
        return $response;
    }

    /**
     * @param $action
     * @param UserDataClass|null $defaultData
     * @return \Symfony\Component\Form\Form
     */
    private function createForm($action, UserDataClass $defaultData)
    {
        /** @var CsrfTokenManager $csrfTokenManager */
        $csrfTokenManager = $this->get('csrf_token.manager');
        /** @var Translator $translator */
        $translator = $this->get('translator');

        $validator = Validation::createValidatorBuilder()
            ->setTranslator($translator)
            ->setTranslationDomain('validators')
            ->getValidator()
        ;

        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->addExtension(new ValidatorExtension($validator))
            ->addExtension(new CsrfExtension($csrfTokenManager, $translator, 'validators'))
            ->addTypeGuesser(new FormTypeGuesser())
            ->getFormFactory();

        $form = $formFactory->createBuilder('form', $defaultData, [
            'action' => '/app.php/crud/' . $action,
            'method' => 'POST',
            'attr' => [
            ]
        ])
            ->add('username', null, ['constraints' => [
                new NotBlank()
            ]])
            ->add('dob', null, ['label' => 'Date of birth'])
            ->add('age')
            ->add('email', null, ['constraints' => [
                new NotBlank(),
                new Email(),
            ]])
            ->add('photo')
            ->add('active')
            ->add('Save', 'submit', [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $formEvent) {
                /** @var UserDataClass $data */
                $data = $formEvent->getData();
                if ($data->username && $data->email) {
                    $data->active = true;
                    $formEvent->setData($data);
                }

                $now = new \DateTime();
                $dob = $data->dob;
                $diff = $now->diff($dob);

                $data->age = $diff->y;
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $formEvent) {
                //log model data
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $formEvent) {
                $data = $formEvent->getData();
                $data['email'] = strtoupper($data['email']);
                $formEvent->setData($data);
            })
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $formEvent) {
                $data = $formEvent->getData();
                $data->username .= '!';
                $formEvent->setData($data);
            })
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $formEvent) {
                $data = $formEvent->getData();

                $formEvent->getForm()->get('username')->addError(new FormError('Test error!'));
            })
            ->getForm();

        return $form;
    }
} 