<?php
declare(strict_types=1);

class Sportif extends Model {

    protected int $id ;
    protected string $username;
    protected string $lastName;
    protected string $firstName;
    protected string $email;
    protected string $birthday;
    protected string $city;
    protected string $address;
    protected string $zipCode;
    protected string $accountDate;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return mixed|string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return mixed|string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return mixed|string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return mixed|string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return mixed|string
     */
    public function getBirthday(): string
    {
        return $this->birthday;
    }

    /**
     * @return mixed|string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return mixed|string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return mixed|string
     */
    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    /**
     * @return string
     */
    public function getAccountDate(): string
    {
        return $this->accountDate;
    }

    /**
     * Constructeur de la classe
     *
     * @param array $data Tableau associatif des données contenant les informations de l'utilisateur
     */
    private function __construct(array $data = [])
    {
        if(count($data) == 0)
            return;

        $this->id = (int)$data["idUser"] ?? -1;
        $this->username = $data["username"] ?? "None";
        $this->lastName = $data["lastName"] ?? "None";
        $this->firstName = $data["firstName"] ?? "None";
        $this->email = $data["email"] ?? "None";
        $this->birthday = $data["birthday"] ?? "None";
        $this->city = $data["city"] ?? "None";
        $this->address = $data["address"] ?? "None";
        $this->zipCode = $data["zipCode"] ?? "None";
        $this->accountDate = $data["accountDate"] ?? date("Y-m-d H:i:s");
    }

    public static function createFromPost(): self
    {
        return new Sportif($_POST);
    }

    /**
     * Récupère un utilisateur depuis son ID
     * @param int $id Id de l'utilisateur
     * @return static L'utilisateur crée
     * @throws Exception Si l'utilisateur est inconnu
     */
    public static function getUserImageById(int $id): string
    {
        $request = MyPDO::getInstance()->prepare(<<<SQL
    SELECT profilePicture
    FROM Sportif
    WHERE idUser = :id
SQL
        );
        $request->bindParam('id', $id);
        $request->setFetchMode(PDO::FETCH_ASSOC);
        $request->execute();

        if ($request->rowCount() == 0) {
            throw new Exception("id inconnu");
        }
        return $request->fetch(PDO::FETCH_COLUMN);
    }

    public static function getProfilePictureURL($id): string {
        return "/Utils/ProfilePicture/$id";
    }

    /**
     * Récupère un utilisateur depuis son ID
     * @param int $id Id de l'utilisateur
     * @return static L'utilisateur crée
     * @throws Exception Si l'utilisateur est inconnu
     */
    public static function getUserInDatabaseById(int $id): self
    {
        $request = MyPDO::getInstance()->prepare(<<<SQL
    SELECT idUser, username, lastName, firstName, email, birthday, city, address, zipCode, accountDate
    FROM Sportif
    WHERE idUser = :id
SQL
        );
        $request->bindParam('id', $id);
        $request->setFetchMode(PDO::FETCH_ASSOC);
        $request->execute();

        if ($request->rowCount() == 0) {
            throw new Exception("id inconnu");
        }
        return new Sportif($request->fetch());
    }

    public static function checkIfUsernameAvailable(string $username): bool
    {
        $request = MyPDO::getInstance()->prepare(<<<SQL
    SELECT NULL
    FROM Sportif
    WHERE username = :username
SQL
        );
        $request->bindParam('username', $username);
        $request->setFetchMode(PDO::FETCH_ASSOC);
        $request->execute();

        return $request->rowCount() == 0;
    }

    public static function checkIfEmailAvailable(string $email): bool
    {
        $request = MyPDO::getInstance()->prepare(<<<SQL
    SELECT NULL
    FROM Sportif
    WHERE email = :email
SQL
        );
        $request->bindParam('email', $email);
        $request->setFetchMode(PDO::FETCH_ASSOC);
        $request->execute();

        return $request->rowCount() == 0;
    }

    /**
     * Créer un contenu HTML utilisé pour l'affichage des informations de l'utilisateur
     *
     * @return string
     */
    public function displayInformation() : string
    {
        $html = <<<HTML
    <dl>
        <dt>Identifiant</dt>
            <dd>{$this->username} ({$this->id})</dd>
        <dt>Nom</dt>
            <dd>{$this->lastName}</dd>
        <dt>Pr&eacute;nom</dt>
            <dd>{$this->firstName}</dd>
        <dt>e-mail</dt>
            <dd>{$this->email}</dd>
        <dt>Date de naissance</dt>
            <dd>{$this->birthday}</dd>
        <dt>Adresse</dt>
            <dd>{$this->address}, {$this->zipCode}, {$this->city}</dd>
    </dl>

HTML;
        return $html;
    }

     /**
     * Ajoute un nouvel utilisateur dans la base de donnée à l'aide des informations entrées par l'utilisateur lui-même.
     *
     * @throws Exception
     */
    public function registerUser(string $encodedPassword, bool $setAsCurrentUser = false): bool {
        $request = MyPDO::getInstance()->prepare(<<<SQL
INSERT INTO Sportif(username, password, firstName, lastName, email, birthday, city, address, zipCode, accountDate)
VALUES(:username, :password, :firstName, :lastName, :email, :birthday, :city, :address, :zipCode, NOW());
SQL
            );
        $request->execute([
            'username' => $this->username,
            'password' => $encodedPassword,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'birthday' => $this->birthday,
            'city' => $this->city,
            'address' => $this->address,
            'zipCode' => $this->zipCode
        ]);
        $this->id = (int)MyPDO::getInstance()->lastInsertId();

        if($setAsCurrentUser) {
            try {
                Session::start();
                $_SESSION[SecureUserAuthentication::SESSION_KEY][SecureUserAuthentication::SESSION_USER_KEY] = $this;
            } catch (SessionException $sessionException) { }
        }

        return true;
    }

    /**
     * @param string $filter
     * @return array
     * @throws Exception
     */
    public static function filterUser(string $filter): array
    {
        $request = MyPDO::getInstance()->prepare(<<<SQL
    SELECT idUser
    FROM Sportif
    WHERE CONCAT(userName, ' ', firstName, ' ', lastName) REGEXP :filterRegex
SQL
        );
        $request->execute([
            'filterRegex' => implode('', array_map(function($s) { return "(?=.*$s.*)"; }, explode(' ', $filter))),
        ]);
        return $request->fetchAll(PDO::FETCH_COLUMN);
    }
}