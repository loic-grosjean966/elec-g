<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Security\LoginFormAuthenticator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use App\Recaptcha\RecaptchaValidator;  // Importation de notre service de validation du captcha
use Symfony\Component\Form\FormError;  // Importation de la classe permettant de créer des erreurs dans les formulaires

class RegistrationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/inscription", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, RecaptchaValidator $recaptcha): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le captcha n'est pas valide, on crée une nouvelle erreur dans le formulaire (ce qui l'empêchera de créer l'article et affichera l'erreur)
            // $request->request->get('g-recaptcha-response')  -----> code envoyé par le captcha dont la méthode verify() a besoin
            // $request->server->get('REMOTE_ADDR') -----> Adresse IP de l'utilisateur dont la méthode verify() a besoin
            if(!$recaptcha->verify( $request->request->get('g-recaptcha-response'), $request->server->get('REMOTE_ADDR') )){

                // Ajout d'une nouvelle erreur manuellement dans le formulaire
                $form->addError(new FormError('Le Captcha doit être validé !'));
            }
            // encode the plain password
            $user
            ->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('noreply@elecg71.fr', 'Noreply'))
                    ->to($user->getEmail())
                    ->subject('Confirmez votre adresse email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // Message flash de succès
            $this->addFlash('success', 'Votre compte a été créé avec succès ! Un email vous a été envoyé pour activer votre compte.');

            // Redirection de l'utilisateur vers la page de connexion
            return $this->redirectToRoute('app_login');

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre adresse email a été vérifié avec succès !');

        return $this->redirectToRoute('app_login');
    }
}
