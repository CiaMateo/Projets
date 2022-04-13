<?php
class Login extends Controller
{
    public function index()
    {
        $authentication = new SecureUserAuthentication();

        try {
            $authentication->getUserFromAuth();
        } catch (AuthenticationException $ae) { }

        if($authentication->isUserConnected())
        {
            header("Location: /");
        }
        else {
            $challenge = $authentication->generateChallenge();
            $this->render('index',
                [   'pageTitle'
                    => 'Connexion',
                    'flexDirection'
                    => 'flex-column',
                    'challenge'
                    => $challenge],
                    'default_no_navbar');
        }
    }
}