<?php

class Participation
{
    /**
     * @return int
     */
    public function getIdParticipant(): int
    {
        return $this->idParticipant;
    }

    /**
     * @return int
     */
    public function getIdActivite(): int
    {
        return $this->idActivite;
    }

    protected int $idParticipant;
    protected int $idActivite;

    /**
     * Constructeur de la classe
     *
     * @param array $data Tableau associatif des données contenant les informations de l'activité
     */
    private function __construct(array $data = [])
    {
        if(count($data) == 0)
            return;

        $this->idImage = (int)$data["idImage"] ?? -1;
        $this->base64Value = $data["image"] ?? "";
    }

    public static function getActivitesByParticpantId(int $id): array
    {
        $request = MyPDO::getInstance()->prepare(<<<SQL
    SELECT idActivity
    FROM Participer
    WHERE idParticipant = :id
SQL
        );
        $request->bindParam('id', $id);
        $request->setFetchMode(PDO::FETCH_ASSOC);
        $request->execute();

        if ($request->rowCount() == 0) {
            return [];
        }
        return $request->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function getParticipantOfActivityById(int $id): array
    {
        $request = MyPDO::getInstance()->prepare(<<<SQL
    SELECT idParticipant
    FROM Participer
    WHERE idActivity = :id
SQL
        );
        $request->bindParam('id', $id);
        $request->setFetchMode(PDO::FETCH_ASSOC);
        $request->execute();

        if ($request->rowCount() == 0) {
            return [];
        }
        return $request->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function registerParticipation(int $idUser, int $idActivite) : bool{
        if(!self::checkParticipation($idUser, $idParticipant)) {
            $request = MyPDO::getInstance()->prepare(<<<SQL
                INSERT INTO Participer(idParticipant, idActivity)
                VALUES(:idUser, :idActivite);
SQL
            );
            $request->execute([
                'idUser' => $idUser,
                'idParticipant' => $idParticipant,
            ]);
            return true;
        }
        return false;
    }

    public static function checkParticipation(int $idUser, int $idActivite) : bool{
    $request = MyPDO::getInstance()->prepare(<<<SQL
            SELECT NULL
            FROM Participer
            WHERE idParticipant = :idUser AND idActivity = :idActivite
SQL
    );
    $request->execute([
        'idUser' => $idUser,
        'idActivite' => $idActivite,
    ]);
    return $request->rowCount() > 0;
}

    public static function removeParticipation(int $idUser, int $idActivite) : void{
        $request = MyPDO::getInstance()->prepare(<<<SQL
            DELETE FROM Participer
            WHERE idParticipant = :idUser AND idActivity = :idActivite
SQL
        );
        $request->execute([
            'idUser' => $idUser,
            'idActivite' => $idActivite,
        ]);
    }
}