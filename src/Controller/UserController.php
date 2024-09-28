<?php

namespace App\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use App\Form\UserSType;
use App\Form\UserSignupType;
use App\Entity\Tutoriel;
use App\Repository\TutorielRepository;
use App\Form\ResetType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\UserLoginType;
use Twilio\Rest\Client;
use Doctrine\ORM\EntityManagerInterface; 
use App\Form\TutorielType; // Ensure you have this form class
use App\Form\UserProfileType;




class UserController extends AbstractController

{
    private $session;
    private $userRepository;
    private $mailer;
    private $tutorielRepository;



    public function __construct(SessionInterface $session, UserRepository $userRepository,MailerInterface $mailer,TutorielRepository $tutorielRepository)
    {
        $this->session = $session;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->tutorielRepository = $tutorielRepository;

    }
    /**
 * @Route("/profile", name="profile")
 */
public function profile(SessionInterface $session, UserRepository $userRepository): Response
{
    // Get the user_id from the session
    $userId = $session->get('user_id');

    // Fetch the user information from the database
    $user = $userRepository->find($userId);

    if (!$user) {
        // Handle the case where the user is not found (e.g., redirect to login)
        return $this->redirectToRoute('Connexionuser');
    }

    return $this->render('profile.html.twig', [
        'user' => $user,
        
    ]);}
    /**
 * @Route("/user/modifier/{id}", name="user_edit_profile")
 */
public function editProfile(
    UserRepository $userRepository,
    Request $request,
    $id,
    SessionInterface $session,
    EntityManagerInterface $em,
    UserPasswordEncoderInterface $passwordEncoder
): Response
{
    // Ensure the user is authenticated
    if (!$session->get('user_authenticated')) {
        return $this->redirectToRoute('Connexionuser');
    }

    // Find the user by ID
    $user = $userRepository->find($id);
    $form = $this->createForm(UserSignupType::class, $user);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        // Define the key for encryption
        $key_password = 'salah';
        
        // Get the plain password from the form data
        $password = $user->getPassword();

        // Encrypt the password
        $encryptedPassword = openssl_encrypt($password, "AES-128-ECB", $key_password);

        // Set the encrypted password
        $user->setPassword($encryptedPassword);

        $em->flush();

        return $this->redirectToRoute('app_search'); // Ensure 'user_profile' route exists
    }

    return $this->render('edit_profile.html.twig', [
        'form' => $form->createView(),
    ]);}
   /**
 * @Route("/user", name="user_new")
 */
public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em): Response
{
    $user = new User();
    $form = $this->createForm(UserSignupType::class, $user);
    $user->setRegistrationDate(new \DateTime());

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Example of password validation
        $password = $user->getPassword();
        
        if (strlen($password) < 8) {
            $this->addFlash('error', 'Password must be at least 8 characters long.');
        } else {
            $key_password = "salah"; // Clé de cryptage
            
            // Crypter le mot de passe 
            $encryptedPassword = openssl_encrypt($password, "AES-128-ECB", $key_password);
            
            // Attribuer le mot de passe crypté à l'objet utilisateur
            $user->setPassword($encryptedPassword);

           
            $user->setRole('ROLE_USER');

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User registered successfully.');
            return $this->redirectToRoute('Connexionuser');
        }
    }

    
    if ($form->isSubmitted() && !$form->isValid()) {
        $errors = $form->getErrors(true, true);
        foreach ($errors as $error) {
            $this->addFlash('error', $error->getMessage());
        }
    }

    return $this->render('signup.html.twig', [
        'form' => $form->createView(),
    ]);
}
    
      /**
 * @Route("/Connexion", name="Connexionuser")
 */
public function login( Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder, SessionInterface $session): Response
{
    $form = $this->createForm(UserLoginType::class);
 

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $email = $data['email'];
        $password = $data['password'];

        // Trouver l'utilisateur par email
        $user = $userRepository->findOneBy(['email' => $email]);

        // Clé de cryptage
        $key_password = "salah";

        // CRYPTER le mot de passe pour la comparaison
        $encrypted_chaine = openssl_encrypt($password, "AES-128-ECB", $key_password);

        if ($user && $user->getPassword() === $encrypted_chaine) {
            // Définir les sessions
            $session->set('user_authenticated', true);
            $session->set('user_id', $user->getUserID());
            $session->set('user_role', $user->getRole());
            $session->set('user_name', $user->getNom());
        
            // Rediriger en fonction du rôle
            if ($user->getRole() === "['ROLE_ADMIN']") {
                return $this->redirectToRoute('afficheuser'); // Remplacez par la route réelle pour le dashboard admin
            } else {
                return $this->redirectToRoute('app_search'); // Remplacez par la route réelle pour la page de recherche utilisateur
            }
        } else {
            // Ajouter un message d'erreur
            $this->addFlash('error', 'Email ou mot de passe incorrect');
        }
    }

    // Rendre le formulaire de connexion
    return $this->render('login.html.twig', [
        'form' => $form->createView(),
    ]);
}

    /**
     * @Route("/add_tutorial", name="add_tutorial", methods={"POST"})
     */
    public function addTutorial(Request $request, EntityManagerInterface $entityManager): Response
    {
        $titre = $request->request->get('titre');
        $description = $request->request->get('description');
        $format = $request->request->get('format');
        $categorie = $request->request->get('categorie');
        $duree = $request->request->get('duree');
        $langue = $request->request->get('langue');
        $file = $request->files->get('file');

        if ($file) {
            // Define the upload directory based on the file format
            $uploadDir = '';
            switch ($format) {
                case '.jpg':
                    $uploadDir = $this->getParameter('kernel.project_dir').'/public/tutoriel/image';
                    break;
                case '.mp4':
                    $uploadDir = $this->getParameter('kernel.project_dir').'/public/tutoriel/video';
                    break;
                case '.pdf':
                    $uploadDir = $this->getParameter('kernel.project_dir').'/public/tutoriel/pdf';
                    break;
                default:
                    $this->addFlash('error', 'Format de fichier non supporté.');
                    return $this->redirectToRoute('afficheuser');
            }

            // Sanitize the `titre` to create a safe file name
            $sanitizedTitle = preg_replace('/[^a-zA-Z0-9-_]/', '_', $titre);

            // Generate the file name using the sanitized title and the file extension
            $fileName = $sanitizedTitle.$format;

            // Move the uploaded file to the appropriate directory
            $file->move($uploadDir, $fileName);

            // Create and save the new Tutoriel entity
            $tutoriel = new Tutoriel();
            $tutoriel->setTitre($titre);
            $tutoriel->setDescription($description);
            $tutoriel->setFormat($format);
            $tutoriel->setCategorie($categorie);
            $tutoriel->setDuree($duree);
            $tutoriel->setLangue($langue);
            $tutoriel->setFilePath($fileName);

            $entityManager->persist($tutoriel);
            $entityManager->flush();

            $this->addFlash('success', 'Tutoriel ajouté avec succès!');
        } else {
            $this->addFlash('error', 'Échec du téléchargement du fichier.');
        }

        return $this->redirectToRoute('afficheuser');
    }

   /**
 * @Route("/tutoriel/edit/{id}", name="edit_tutoriel")
 */
public function editTutorial(Request $request, int $id, SessionInterface $session, EntityManagerInterface $em): Response
{
    if (!$session->get('user_authenticated')) {
        return $this->redirectToRoute('Connexionuser');
    }

    $tutoriel = $this->tutorielRepository->find($id);
    if (!$tutoriel) {
        throw $this->createNotFoundException('Tutoriel not found');
    }

    $form = $this->createForm(TutorielType::class, $tutoriel);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Handle the file upload
        /** @var UploadedFile $file */
        $file = $form->get('filePath')->getData();

        if ($file) {
            $newFilename = uniqid().'.'.$file->guessExtension();

            try {
                $file->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // Handle the exception if something happens during file upload
            }

            // Update the 'filePath' property to store the file name
            $tutoriel->setFilePath($newFilename);
        }

        $em->flush();

        return $this->redirectToRoute('gestion_tutoriel');
    }

    return $this->render('edit.html.twig', [
        'form' => $form->createView(),
    ]);
    }
     
    
   /**
 * @Route("/gestion-tutoriel", name="gestion_tutoriel")
 */
public function gestionTutoriel(SessionInterface $session, TutorielRepository $tutorielRepository): Response
{
    // Check if the user is authenticated
    if (!$session->get('user_authenticated')) {
        return $this->redirectToRoute('Connexionuser');
    }

    // Fetch tutorials from the database
    $tutoriels = $tutorielRepository->findAll();

    return $this->render('gestion_tutoriel.html.twig', [
        'tutoriels' => $tutoriels,
    ]);
}


  /**
 * @Route("/gestion-utilisateur", name="gestion_utilisateur")
 */
public function gestionUtilisateur(SessionInterface $session, UserRepository $userRepository): Response
{
    // Check if the user is authenticated
    if (!$session->get('user_authenticated')) {
        return $this->redirectToRoute('Connexionuser');
    }

    // Fetch users from the database
    $users = $userRepository->findAll();

    return $this->render('gestion_utilisateur.html.twig', [
        'users' => $users,
    ]);
}



    /**
     * @Route("/tutoriel/delete/{id}", name="delete_tutoriel", methods={"POST"})
     */
    public function deleteTutorial(int $id, SessionInterface $session, EntityManagerInterface $em): Response
    {
        if (!$session->get('user_authenticated')) {
            return $this->redirectToRoute('Connexionuser');
        }

        $tutoriel = $this->tutorielRepository->find($id);
        if (!$tutoriel) {
            throw $this->createNotFoundException('Tutoriel not found');
        }

        $em->remove($tutoriel);
        $em->flush();

        return $this->redirectToRoute('gestion_tutoriel'); // Adjust route as needed
    }
        

       /**
 * @Route("/afficheuser", name="afficheuser")
 */
public function affiche(SessionInterface $session, UserRepository $userRepository, TutorielRepository $tutorielRepository): Response
{
    if (!$session->get('user_authenticated') || $session->get('Role')==="['ROLE_ADMIN']" ) {
        return $this->redirectToRoute('Connexionuser');
    }

    $users = $userRepository->findAll();
    $tutoriels = $tutorielRepository->findAll();

    return $this->render('dashboard_admin.html.twig', [
        'users' => $users,
        'tutoriels' => $tutoriels,
    ]);
}

    
    
    
    
    
        /**
 * @Route("/user/modifier/{id}", name="u")
 */
public function modifier(UserRepository $userRepository, Request $request, $id, SessionInterface $session, EntityManagerInterface $em): Response
{
    if (!$session->get('user_authenticated')) {
        return $this->redirectToRoute('Connexionuser');
    }

    $user = $userRepository->find($id);
    $form = $this->createForm(UserSignupType::class, $user);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        // Define the key for encryption
        $key_password = 'salah';
        
        // Get the plain password from the form data
        $password = $user->getPassword();

        // Encrypt the password
        $encryptedPassword = openssl_encrypt($password, "AES-128-ECB", $key_password);

        // Set the encrypted password
        $user->setPassword($encryptedPassword);

        $em->flush();
        return $this->redirectToRoute('gestion_utilisateur');
    }

    return $this->render('modifier.html.twig', [
        'form' => $form->createView(),
    ]);
}

    
        /**
         * @Route("/user/supp/{id}", name="d")
         */
        public function supprimer($id, SessionInterface $session, EntityManagerInterface $em): Response
        {
            if (!$session->get('user_authenticated')) {
              
                return $this->redirectToRoute('Connexionuser');
            }
    
            $user = $em->getRepository(User::class)->find($id);
            $em->remove($user);
            $em->flush();
    
            return $this->redirectToRoute('gestion_utilisateur');
        }
    
       /**
 * @Route("/Ajouteuseradmin", name="Ajouteuseradmin")
 */
public function ajouteradmin(Request $request, SessionInterface $session, EntityManagerInterface $em): Response
{
    if (!$session->get('user_authenticated')) {
        return $this->redirectToRoute('Connexionuser');
    }

    $user = new User();
    $form = $this->createForm(UserSignupType::class, $user);
    $form->handleRequest($request);
    $user->setRegistrationDate(new \DateTime());

    if ($form->isSubmitted() && $form->isValid()) {
        // Define the key for encryption
        $key_password = 'salah';
        
        // Get the plain password from the form data
        $password = $user->getPassword();

        // Encrypt the password
        $encryptedPassword = openssl_encrypt($password, "AES-128-ECB", $key_password);

        // Set the encrypted password
        $user->setPassword($encryptedPassword);

        // Persist the user
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('afficheuser');
    }

    return $this->render('ajouter.html.twig', [
        'form' => $form->createView(),
    ]);
}

    

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): Response
    {
        $form = $this->createForm(UserLoginType::class);
        $session->remove('user_authenticated');
        $session->remove('user_id');
        
        // Invalider la session pour s'assurer que toutes les informations de session sont supprimées
        $session->invalidate();
        return $this->redirectToRoute('Connexionuser');

       
    }
    
    /**
     * @Route("/reset-password", name="app_reset_password")
     */
    public function resetPassword(Request $request, SessionInterface $session, MailerInterface $mailer, EntityManagerInterface $em): Response
    {
        $user = new User(); 
        $form = $this->createForm(ResetType::class, $user); // Associez le formulaire à l'objet User

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $user->getEmail();


        if ($email) {
            // Search for the user by email
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                $notificationMessage = 'Invalid email address: ' . $email;
                $session->getFlashBag()->add('error', $notificationMessage);
                return $this->redirectToRoute('Connexionuser');
            }

            $password = $user->getPassword();
            $key_password="salah";
            $decrypted_chaine = openssl_decrypt($password, "AES-128-ECB" ,$key_password);
  
            $emaill = (new Email())
            ->from('lefriiw@gmail.com')
            ->to($email)
            ->subject('Réinitialisation du mot de passe')
            ->text('')
            ->html('<p> Votre mot de passe: ' . $decrypted_chaine. '</p>');
            $mailer->send($emaill);
            $notificationMessage = 'Votre Mot de passe a étè envoyer à : ' . $email;
            $session->getFlashBag()->add('success', $notificationMessage);
  return $this->redirectToRoute('Connexionuser');

            try {
                $mailer->send($emailMessage);
                $notificationMessage = 'Your password has been sent to: ' . $email;
                $session->getFlashBag()->add('success', $notificationMessage);
            } catch (\Exception $e) {
                $notificationMessage = 'Failed to send email: ' . $e->getMessage();
                $session->getFlashBag()->add('error', $notificationMessage);
            }

            return $this->redirectToRoute('Connexionuser');
        }
    }
        return $this->render('reset.html.twig', [
                'form' => $form->createView()
            ]);
    }
}