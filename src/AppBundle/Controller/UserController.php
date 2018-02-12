<?php


namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserController extends Controller
{

    /**
     * @Route("/login2", name="login2")
     */
    public function login2Action(Request $request, AuthenticationUtils $authUtils)
    {

        $error = $authUtils->getLastAuthenticationError();
        $lastUser = $authUtils->getLastUsername();

        // replace this example code with whatever you need
        return $this->render('user/login.html.twig', [
            'err' => $error,
            'lastUser' => $lastUser
        ]);
    }

    /**
     * @Route("/register2", name="user_register2")
     */
    public function register2Action(Request $request)
    {

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('products_index', []);
        }

        $user = new User();
        $formBuilder = $this->createFormBuilder($user);
        $formBuilder
            ->add('name', TextType::class, [
                'label' => 'Username',
                'required' => false
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'required' => false
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => false
            ])
            ->add('age', NumberType::class, [
                'label' => 'Age',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Create account'
            ]);
        $form = $formBuilder->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newUser = $form->getData();
            $pwd = password_hash($newUser->getPassword(), PASSWORD_BCRYPT);
            $newUser->setPassword($pwd);
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();
            try {
                $em->persist($newUser);
                $em->flush();
                $em->getConnection()->commit();

                $token = new UsernamePasswordToken($newUser, null, 'main', $newUser->getRoles());
                $this->get('security.token_storage')->setToken($token);
                $this->get('session')->set('_security_main', serialize($token));

                return $this->redirectToRoute('products_index', []);
            } catch (\Exception $e) {
                $em->getConnection()->rollback();
                throw $e;
            }
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
