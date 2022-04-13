<?php
class Register extends Controller
{
    public function index()
    {
        if(PHPUtils::isPostSetNotEmpty('username', 'firstName', 'lastName', 'email', 'birthday', 'city', 'address', 'zipCode')) {
            $user = Sportif::createFromPost();
            if ($user->registerUser($_POST['password'], true)) {
                header('Location: login');
            }
        }
        else {
            $this->render('index',
                ['pageTitle'
                => 'Inscription',
                    'flexDirection'
                    => 'flex-column'],
                'default_no_navbar');
        }
    }

    public const REGISTER_VALID = "L'identifiant et l'adresse e-mail sont disponible";
    public const REGISTER_INVALID_USERNAME = "L'identifiant est déjà utilisé.";
    public const REGISTER_INVALID_EMAIL = "L'adresse e-mail est déjà utilisée.";

    public function verifyInformation()
    {
        if(PHPUtils::isPostSetNotEmpty('username', 'email'))
        {
            if(!Sportif::checkIfUsernameAvailable($_POST["username"]))
            {
                echo self::REGISTER_INVALID_USERNAME;
                die;
            }
            if(!Sportif::checkIfEmailAvailable($_POST["email"]))
            {
                echo self::REGISTER_INVALID_EMAIL;
                die;
            }
        }
        echo self::REGISTER_VALID;
    }
}