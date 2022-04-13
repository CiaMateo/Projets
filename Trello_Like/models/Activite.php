<?php


class Activite extends Model
{
    protected int $idPub;
    protected int $idOrganizer;
    protected string $title;
    protected string $content;
    protected string $date;
    protected string $eventDate;
    protected string $place;
    protected string $skillLevel;

    /**
     * @return int
     */
    public function getIdPub(): int
    {
        return $this->idPub;
    }

    /**
     * @return int
     */
    public function getIdOrganizer(): int
    {
        return $this->idOrganizer;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    public function getFormatedDate(): string
    {
        setlocale(LC_TIME, 'fr_FR','fra'); // I'm french !
        $date = new DateTime($this->date);
        return utf8_encode(strftime("%e %b %Y", $date->getTimestamp()));
    }

    /**
     * @return string
     */
    public function getEventDate(): string
    {
        return $this->eventDate;
    }

    public function getFormatedEventDate(): string
    {
        setlocale(LC_TIME, 'fr_FR','fra'); // I'm french !
        $date = new DateTime($this->eventDate);
        return utf8_encode(strftime("%e %b %Y", $date->getTimestamp()));
    }

    public function getFormatedEventTime(): string
    {
        setlocale(LC_TIME, 'fr_FR','fra'); // I'm french !
        $date = new DateTime($this->eventDate);
        return utf8_encode(strftime("%Hh%M", $date->getTimestamp()));
    }

    /**
     * @return string
     */
    public function getPlace(): string
    {
        return $this->place;
    }

    /**
     * @return string
     */
    public function getSkillLevel(): string
    {
        switch ($this->skillLevel)
        {
            case 'A':
                return 'Amateur';
            case 'D':
                return 'Débutant';
            case 'C':
                return 'Confirmé';
            case 'P':
                return 'Professionnel';
            default:
                return '';
        }
    }

    public function searchActivity(string $param)
    {
        if (empty($param))
            die;
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

        $this->idPub = (int)$data["idPub"] ?? -1;
        $this->idOrganizer = (int)$data["idOrganizer"] ?? -1;
        $this->title = $data["title"] ?? -1;
        $this->content = $data["content"] ?? -1;
        $this->date = $data["date"] ?? -1;
        $this->eventDate = $data["eventDate"] ?? -1;
        $this->place = $data["place"] ?? -1;
        $this->skillLevel = $data["skillLevel"] ?? -1;
    }

    public static function getActiviteById(int $id): self
    {
        $request = MyPDO::getInstance()->prepare(<<<SQL
    SELECT idPub, idOrganizer, title, content, date, eventDate, place, skillLevel
    FROM Activite
    WHERE idPub = :id
SQL
        );
        $request->bindParam('id', $id);
        $request->setFetchMode(PDO::FETCH_ASSOC);
        $request->execute();

        if ($request->rowCount() == 0) {
            throw new Exception("id inconnu");
        }
        return new Activite($request->fetch());
    }

    public static function getAllActivities(): array
    {
        $request = MyPDO::getInstance()->prepare(<<<SQL
    SELECT *
    FROM Activite
SQL
        );
        $request->setFetchMode(PDO::FETCH_ASSOC);
        $request->execute();
        return array_map(function ($e) { return new Activite($e); }, $request->fetchAll());
    }

    public static function getActivities(int $start, int $limit): array
    {
        $request = MyPDO::getInstance()->prepare(<<<SQL
    SELECT *
    FROM Activite
    LIMIT :start, :limit;
SQL
        );
        $request->bindParam('start', $start, PDO::PARAM_INT);
        $request->bindParam('limit', $limit, PDO::PARAM_INT);
        $request->setFetchMode(PDO::FETCH_ASSOC);
        $request->execute();
        return array_map(function ($e) { return new Activite($e); }, $request->fetchAll());
    }

    public function getImagesId(): array
    {
        $request = MyPDO::getInstance()->prepare(<<<SQL
    SELECT i.idImage
    FROM AttacherActivite i, Activite a
    WHERE i.idPub = :idPub
SQL
        );
        $request->bindParam('idPub', $this->idPub);
        $request->setFetchMode(PDO::FETCH_ASSOC);
        $request->execute();

        if ($request->rowCount() == 0) {
            return [];
        }
        return array_map(function($e) {
            return $e['idImage'];
        }, $request->fetchAll());
    }

    public static function filterActivities(int $start, int $limit, array $filtre):array
    {
        $pdostmt = MyPDO::getInstance()->prepare(<<<SQL
SELECT *
FROM Activite a JOIN Sportif s ON a.idOrganizer = s.idUser
WHERE UPPER(title) LIKE UPPER(:titleFilter)
AND UPPER(content) LIKE UPPER(:contentFilter)
AND (UPPER(lastName) LIKE UPPER(:authorFilter) OR UPPER(firstName) LIKE UPPER(:authorFilter))
AND UPPER(place) LIKE UPPER(:placeFilter)
AND UPPER(skillLevel) LIKE UPPER(:skillLevelFilter)
AND eventDate >= STR_TO_DATE(:dateAfterFilter, '%Y-%m-%d')
AND eventDate <= STR_TO_DATE(:dateBeforeFilter, '%Y-%m-%d')

ORDER BY date
LIMIT :start, :limit;
SQL
            );

        $pdostmt->setFetchMode(PDO::FETCH_ASSOC);
        $pdostmt->bindValue('start', $start, PDO::PARAM_INT);
        $pdostmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $pdostmt->bindValue('titleFilter', "%{$filtre['title']}%");
        $pdostmt->bindValue('contentFilter', "%{$filtre['content']}%");
        $pdostmt->bindValue('authorFilter', "%{$filtre['author']}%");
        $pdostmt->bindValue('placeFilter', "%{$filtre['place']}%");
        $pdostmt->bindValue('skillLevelFilter', "%{$filtre['skillLevel']}%");
        $pdostmt->bindValue('dateAfterFilter', $filtre['dateAfter']);
        $pdostmt->bindValue('dateBeforeFilter', $filtre['dateBefore']);

        $pdostmt->execute();

        return array_map(function ($e) { return new Activite($e); }, $pdostmt->fetchAll());
    }

    public static function creerActivite(array $info):void{
        $stmt = MyPDO::getInstance()->prepare(<<<SQL
INSERT INTO Activite(idOrganizer, title, content, eventDate, date, skillLevel, place)
VALUES(:idOrganizer, :title, :content, STR_TO_DATE(:eventDate, '%Y-%m-%d'), NOW(), :skillevel, :place);
SQL);
        $stmt->execute([
            'idOrganizer' => $info['id'],
            'title' => $info['title'],
            'content'=>$info['content'],
            'eventDate' => $info['date'],
            'skillevel' => $info['skill'],
            'place' => $info['place'],
        ]);
    }
}