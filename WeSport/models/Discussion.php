<?php

class Discussion extends Models
{
    protected int $idUser;
    protected int $idGroupe=0;
    protected array $members=[];

    public function __construct(int $idUser, int $idGroupe )
    {
        $this->idGroupe=$idGroupe;
        $this->idUser=$idUser;
    }

    public function getIdMemberGroupe():void
    {
        if ($this->idGroupe==0){
            throw new InvalidArgumentException("Les donn�es sont vides ou n'existe pas");
        }
        $pdostmt = MyPDO::getInstance()->prepare(<<<SQL
SELECT Membre.idUser, Sportif.username
FROM Membre
JOIN Sportif on (Membre.idUser=Sportif.idUser)
WHERE idGroup = :id 
ORDER BY 1;
SQL
);
        $pdostmt->bindValue("id",$this->idGroupe);
        $pdostmt->execute();
        foreach($pdostmt->fecthAll() as $id){
            $this->members[$id];
        }
    }

    public function getMessages():array
    {
        if (empty($this->members)){
            throw new InvalidArgumentException("Aucun mebre dans ce groupe de discussions");
        }
        $pdostmt= MyPDO::getInstance()->prepare(<<<SQL
SELECT date , content , idSender
FROM Message
WHERE idGroup = :id_groupe
ORDER BY date;
SQL
);
        $pdostmt->bindValue("id_groupe",$this->idGroupe);
        $pdostmt->execute();
        return $pdostmt->fecthAll();
    }

    public function afficheMessage():string
    {
        $html="<div>";
        foreach($this->getMessages() as $messages){
            /**Insérer affichage des messages en html, tout est stocker dans la variable $messages  */
        }
        return $html.="</div>";
    }
}
