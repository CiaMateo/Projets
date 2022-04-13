<?php
declare(strict_types=1);

class Image extends Model
{
    /**
     * @return int
     */
    public function getIdImage(): int
    {
        return $this->idImage;
    }

    /**
     * @return string
     */
    public function getBase64Value(): string
    {
        return $this->base64Value;
    }

    protected int $idImage;
    protected string $base64Value;

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

    public static function getImageById(int $id): self {

        $request = MyPDO::getInstance()->prepare(<<<SQL
    SELECT idImage, image
    FROM Image
    WHERE idImage = :id
SQL
        );
        $request->bindParam('id', $id);
        $request->setFetchMode(PDO::FETCH_ASSOC);
        $request->execute();

        if ($request->rowCount() == 0) {
            throw new Exception("id inconnu");
        }
        return new Image($request->fetch());
    }

    public static function getURLById($id): string {
        return "/Utils/Image/$id";
    }
}