<?php
declare(strict_types=1);

class SecureUserAuthentication extends Model
{
    const SESSION_KEY = '__UserAuthentication__';
    const SESSION_USER_KEY = 'user';

    const LOGOUT_INPUT_NAME = "logout";

    const CODE_INPUT_NAME = 'code';
    const SESSION_CHALLENGE_KEY = 'challenge';
    const RANDOM_STRING_SIZE = 16;

    protected ?Sportif $user = null;

    /**
     * Constructeur de la classe
     */
    public function __construct()
    {
        try {
            $this->user = $this->getUserFromSession();
        } catch (NotLoggedInException $nlie) {
        }
    }

    /**
     * Définit l'utilisateur connecté (en session et localement)
     *
     * @param Sportif $user Utilisateur connecté
     */
    protected function setUser(Sportif $user): void
    {
        Session::start();
        $this->user = $user;
        $_SESSION[self::SESSION_KEY][self::SESSION_USER_KEY] = $user;
    }

    public function getProfileOptionButton(): string {
        if($this->isUserConnected()) {
            $logout_input_name = SecureUserAuthentication::LOGOUT_INPUT_NAME;
            $username = $this->getUser()->getUsername();
            $userInformation = $this->getUser()->displayInformation();
            return <<<HTML
            <button class="btn btn-main round-input shadow-sm" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                <span>$username<i class="bi bi-person-circle ms-2"></i></span>
            </button>
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"
                 aria-labelledby="profileOffcanvasLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="profileOffcanvasLabel">Profile</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                </div>
                <div class="offcanvas-body d-flex flex-column flex-grow-1">
                    <div class="flex-grow-1">
                    $userInformation
                    </div>
                    <form method="POST" action="/">
                        <button class="btn btn-main round-input w-100"
                                name="$logout_input_name">
                            Déconnexion<i class="bi bi-door-open-fill ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>
HTML;
        }
        else {
            return <<<HTML
            <a href="/login">
            <button class="btn btn-main round-input shadow-sm" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                <span>Connexion<i class="bi bi-shield-lock-fill ms-2"></i></span>
            </button>
            </a>
HTML;
        }
    }

    /**
     * Permet de savoir si un utilisateur est connecté (utilisation de session)
     *
     * @return bool Vrai si un utilisateur est connecté
     */
    public function isUserConnected(): bool
    {
        try {
            Session::start();
        } catch (SessionException $e) {
            echo $e->getMessage();
            return false;
        }

        if (!isset($_SESSION[self::SESSION_KEY]))
            return false;

        if (!isset($_SESSION[self::SESSION_KEY][self::SESSION_USER_KEY]))
            return false;

        return $_SESSION[self::SESSION_KEY][self::SESSION_USER_KEY] instanceof Sportif;
    }

    /**
     * Récupère l'utilisateur en cache de la classe
     *
     * @return Sportif|null Retourne null si aucun utilisateur est en cache
     */
    public function getUser(): ?Sportif
    {
        if (isset($this->user))
            return $this->user;

        throw new NotLoggedInException("Utilisateur non connecté");
    }

    /**
     * Récupère l'utilisateur stocké dans la session
     *
     * @return Feed Utilisateur stocké dans la session
     * @throws NotLoggedInException Si aucun utilisateur n'est stocké dans la session
     */
    public function getUserFromSession(): Sportif
    {
        if ($this->isUserConnected())
            return $_SESSION[self::SESSION_KEY][self::SESSION_USER_KEY];

        throw new NotLoggedInException("Utilisateur non connecté");
    }

    /**
     * Deconnecte l'utilisateur actuellement connecté
     */
    public function logoutIfRequested(): void
    {
        if (!isset($_POST[self::LOGOUT_INPUT_NAME]))
            return;

        unset($_SESSION[self::SESSION_KEY][self::SESSION_USER_KEY]);
        unset($this->user);
    }

    /**
     * Créer un formulaire de connexion HTML
     *
     * @param string $action Action du formulaire
     * @param string $submitText Texte du bouton de connexion
     * @return string HTML du formulaire créé
     */
    public function generateChallenge(): string
    {
        $tirageAlea = Random::string(self::RANDOM_STRING_SIZE);

        try{
            Session::start();
            $_SESSION[self::SESSION_KEY][self::SESSION_CHALLENGE_KEY] = $tirageAlea;
        }
        catch (SessionException $se) { }

        return $tirageAlea;
    }

    /**
     * Récupère l'utilisateur depuis les informations de connexion
     *
     * @return Sportif Utilisateur trouvé
     * @throws AuthenticationException Si aucun utilisateur ne correspond aux informations de connexion
     */
    public function getUserFromAuth(): Sportif
    {
        if(isset($_POST[self::CODE_INPUT_NAME]) && !empty($_POST[self::CODE_INPUT_NAME]))
        {
            Session::start();

            $code = $_POST[self::CODE_INPUT_NAME];
            $challenge = $_SESSION[self::SESSION_KEY][self::SESSION_CHALLENGE_KEY];

            $request = MyPDO::getInstance()->prepare(<<<SQL
    SELECT idUser FROM Sportif 
    WHERE :code = SHA2(CONCAT(password, '$challenge', SHA2(LOWER(username), 512)), 512)
SQL
            );

            $request->bindParam("code", $code);
            $request->setFetchMode(PDO::FETCH_ASSOC);

            $request->execute();

            if ($request->rowCount() == 0) {
                throw new AuthenticationException("No user corresponding to the information given");
            }

            $user = Sportif::getUserInDatabaseById((int)$request->fetch()["idUser"]);
            $this->setUser($user);
            return $this->user;
        }
        throw new AuthenticationException("No information given");
    }
}
