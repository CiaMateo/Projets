<?php
class Main extends Controller
{
    public function index(int $i = 0)
    {
        $authentication = new SecureUserAuthentication();

        $authentication->logoutIfRequested();

        $this->render('index', ['pageTitle' => "Flux d'information", 'navbar' => Navbar::getFeedSearchNavbar(), 'pageId' => $i]);
    }

    public function create()
    {
        $authentication = new SecureUserAuthentication();
        if(!$authentication->isUserConnected())
        {
            header('Location: /');
        }

        if(PHPUtils::isPostSetNotEmpty('title', 'content', 'date', 'skill', 'place', 'id'))
        {
            Activite::creerActivite($_POST);
            header('Location: /');
        }

        $this->render('create', ['pageTitle' => "Créer une page d'information", 'navbar' => Navbar::getBasicNavbar()]);
    }

    public function map()
    {
        $this->render('map', ['pageTitle' => "Carte des activités", 'navbar' => Navbar::getBasicNavbar()], 'default_map');
    }

    public function get()
    {
        $activites = Activite::getAllActivities();
        echo json_encode($activites, JSON_PRETTY_PRINT);
    }
}