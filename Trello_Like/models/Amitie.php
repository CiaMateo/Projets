<?php
declare(strict_types=1);

class Amitie extends Model
{
    protected int $idUser1;
    protected int $idUser2;
    protected string $date;

    /**
     * @return int
     */
    public function getIdUser1(): int
    {
        return $this->idUser1;
    }

    /**
     * @return int
     */
    public function getIdUser2(): int
    {
        return $this->idUser2;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * Constructeur de la classe
     *
     * @param array $data Tableau associatif des données contenant les informations de l'activité
     */
    private function __construct(array $data = [])
    {
        if(count($data) == 0)
            return;

            $this->idUser1 = (int)$data["idUser1"] ?? -1;
            $this->idUser2 = (int)$data["idUser2"] ?? -1;
            $this->date = $data["date"] ?? "";
    }

    /**
     * Récupère les amis d'une personne
     *
     * @param int $idUser
     * @return array
     * @throws Exception
     */
    public static function getFriends(int $idUser) : array
    {
        $request = MyPDO::getInstance()->prepare(<<<SQL
            SELECT idUser1 FROM Amitie WHERE idUser2 = :idUser
            UNION
            SELECT idUser2 FROM Amitie WHERE idUser1 = :idUser
            ORDER BY 1;
SQL
        );

        $request->execute([
            'idUser' => $idUser,
        ]);
        $results = $request->fetchAll(PDO::FETCH_COLUMN);

        $amities = [];
        foreach ($results as $result) {
            $amities[] = new Amitie($result);
        }
        return $amities;
    }

    /**
     * Créer une amitié entre 2 Sportifs.
     *
     * @param int $idUser1
     * @param int $idUser2
     * @return bool
     * @throws Exception
     */
    public static function registerFriend(int $idUser1, int $idUser2) : bool{
        if(!self::checkFriend($idUser1, $idUser2)) {
            $request = MyPDO::getInstance()->prepare(<<<SQL
                INSERT INTO Amitie(date, idUser1, idUser2)
                VALUES(NOW(), :idUser1, :idUser2);
SQL
            );
            $request->execute([
                'idUser1' => $idUser1,
                'idUser2' => $idUser2,
            ]);
            return true;
        }
        return false;
    }

    /**
     * Vérifie l'amitié entre 2 personnes
     *
     * @param int $idUser1
     * @param int $idUser2
     * @return bool
     * @throws Exception
     */
    public static function checkFriend(int $idUser1, int $idUser2) : bool{
        $request = MyPDO::getInstance()->prepare(<<<SQL
            SELECT NULL
            FROM Amitie
            WHERE (:idUser1, :idUser2) IN (SELECT idUser1, idUser2
                FROM Amitie)
            OR (:idUser1, :idUser2) IN (SELECT idUser2, idUser1 
                FROM Amitie);
SQL
        );
        $request->execute([
            'idUser1' => $idUser1,
            'idUser2' => $idUser2,
        ]);
        return $request->rowCount() > 0;
    }

    /**
     * Supprime l'amitié entre 2 personnes
     *
     * @param int $idUser1
     * @param int $idUser2
     * @throws Exception
     */
    public static function removeFriend(int $idUser1, int $idUser2) : void{
        $request = MyPDO::getInstance()->prepare(<<<SQL
            DELETE FROM Amitie
            WHERE 
                (idUser1 = :idUser1 AND
                idUser2 = :idUser2)
                OR
                (idUser2 = :idUser1 AND
                idUser1 = :idUser2)
SQL
        );
        $request->execute([
            'idUser1' => $idUser1,
            'idUser2' => $idUser2,
        ]);
    }

    /**
     * Filtre les amis
     *
     * @param int $idUser
     * @param string $filter
     * @return array
     * @throws Exception
     */
    public static function filterFriends(int $idUser, string $filter) : array{

        $request = MyPDO::getInstance()->prepare(<<<SQL
    SELECT idUser
    FROM Sportif
    WHERE CONCAT(userName, ' ', firstName, ' ', lastName) REGEXP :filterRegex
    AND idUser IN (SELECT idUser1 FROM Amitie WHERE idUser2 = :idUser
            UNION
            SELECT idUser2 FROM Amitie WHERE idUser1 = :idUser)
SQL
        );
        $request->execute([
            'idUser' => $idUser,
            'filterRegex' => implode('', array_map(function($s) { return "(?=.*$s.*)"; }, explode(' ', $filter))),
        ]);
        $results = $request->fetchAll(PDO::FETCH_COLUMN);

        $amities = [];
        foreach ($results as $result) {
            $amities[] = new Amitie($result);
        }
        return $amities;
    }
}