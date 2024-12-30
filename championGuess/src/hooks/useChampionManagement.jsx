import { useState, useEffect } from "react";
import axios from "axios";

// Hook personnalisé de gestion des données relatives aux champions
function useChampionManagement() {
  const [championList, setChampionList] = useState([]); //Liste de tous les champions
  const [championListNotGuessYet, setChampionListNotGuessYet] = useState([]); //Liste de tous les champions qui n'ont pas encore été trouvé juste
  const [championListGuessRightOneTime, setChampionListGuessRightOneTime] =
    useState([]); //Liste des champions trouvés juste une fois
  const [championId, setChampionId] = useState(""); // Id du champion actuel
  const [championName, setChampionName] = useState(""); // Nom du champion actuel
  const [championCooldown, setChampionCooldown] = useState([]); // Cooldowns du champion
  const [IsLoaded, setIsLoaded] = useState(false); //Vrai quand l'image est chargé
  const [restart, setRestart] = useState(false); //Vrai quand il n'y a plus de personnage à deviner, relance la recherche de champion
  const [operationComplete, setOperationComplete] = useState(false); //Vrai quand les ajouts et retraits de personnages dans les listes sont terminé

  useEffect(() => {
    // Récupère la liste des champions
    axios
      .get(
        "https://ddragon.leagueoflegends.com/cdn/14.24.1/data/fr_FR/champion.json"
      )
      .then((res) => {
        // Liste complète des champions avec toutes les informations
        const champions = Object.values(res.data.data).map((champ) => ({
          name: champ.name,
          id: champ.id,
          splashArt: `https://ddragon.leagueoflegends.com/cdn/14.24.1/img/champion/${champ.id}.png`,
        }));

        // Liste simplifiée pour championListNotGuessYet (uniquement name et id)
        const simplifiedChampions = champions.map(({ name, id }) => ({
          name,
          id,
        }));
        //Ajout des listes
        setChampionList(champions);
        setChampionListNotGuessYet(simplifiedChampions);
        // Sélectionne un champion aléatoire dès que la liste est chargée
        const random = Math.floor(Math.random() * champions.length);
        const randomChampion = champions[random];
        setChampionId(randomChampion.id);
        setChampionName(randomChampion.name);
        if (restart) {
          setRestart(false);
        }
      })
      .catch((error) => console.error("Error fetching champions list:", error));
  }, [restart]);

  useEffect(() => {
    if (championId) {
      // Récupère les données du champion sélectionné
      axios
        .get(
          `https://ddragon.leagueoflegends.com/cdn/14.24.1/data/fr_FR/champion/${championId}.json`
        )
        .then((res) => {
          const dataChampion = res.data;
          setChampionCooldown(dataChampion.data[championId].spells[3].cooldown);
        })
        .catch((error) => {
          console.error("Error fetching champion data:", error);
        });
    }
  }, [championId]);

  //Fonction de sélection d'un personnage optimisant l'aléatoire
  const setRandomChampion = () => {
    const random = Math.floor(Math.random() * 100);
    let randomChampion;
    // Cas où il ne reste qu'un seul champion possible
    if (
      championListNotGuessYet.length === 1 &&
      championListGuessRightOneTime.length === 0
    ) {
      const lastChampion = championListNotGuessYet[0];
      if (lastChampion.id === championId) {
        setRestart(true);
        return;
      }
    }
    // Autre cas où il ne reste qu'un champion
    if (
      championListNotGuessYet.length === 0 &&
      championListGuessRightOneTime.length === 1
    ) {
      const lastChampion = championListGuessRightOneTime[0];
      if (lastChampion.id === championId) {
        setRestart(true);
        return;
      }
    }
    //Il ne reste aucun champion
    if (
      championListGuessRightOneTime.length === 0 &&
      championListNotGuessYet.length === 0
    ) {
      setRestart(true);
    }
    //Cas où la liste de personnage trouvé est vide
    else if (championListGuessRightOneTime.length === 0) {
      randomChampion = championListNotGuessYet[random];
    }
    //Cas où tous les personnages ont été trouvé une fois
    else if (championListNotGuessYet.length === 0) {
      let randomAlreadyGuess = Math.floor(
        Math.random() * championListGuessRightOneTime.length
      );
      randomChampion = championListNotGuessYet[randomAlreadyGuess];
    }
    // Il y a des personnages dans les 2 listes
    else {
      let randomProb = Math.floor(Math.random() * 100);
      if (randomProb <= 97) {
        randomChampion = championListNotGuessYet[random];
      } else {
        let randomAlreadyGuess = Math.floor(
          Math.random() * championListGuessRightOneTime.length
        );
        randomChampion = championListGuessRightOneTime[randomAlreadyGuess];
      }
    }
    // Si le champion sélectionné est le même, on relance la fonction
    if (randomChampion.id === championId) {
      setRandomChampion();
    } else {
      setChampionId(randomChampion.id);
      setChampionName(randomChampion.name);
      setIsLoaded(false);
    }
  };

  //Ajout d'un personnage
  const setNewChampion = (champion) => {
    setChampionId(champion.id);
    setChampionName(champion.name);
    setIsLoaded(false);
  };

  //Toutes les informations ont été chargées
  const setTheEndOfLoading = () => {
    setIsLoaded(true);
  };

  //Un champion vient d'être trouvé juste
  const addChampionsPerfectOnce = (champion) => {
    setChampionListGuessRightOneTime((prevList) => [...prevList, champion]);
    setChampionListNotGuessYet((prevList) =>
      prevList.filter((item) => item.id !== champion.id)
    );
    setTimeout(() => setOperationComplete(true), 0);
  };

  //Un champion vient d'être trouvé deux fois sans erreurs
  const deleteChampionsPerfectTwice = (champion) => {
    setChampionListGuessRightOneTime((prevList) =>
      prevList.filter((item) => item.id !== champion.id)
    );
    setTimeout(() => setOperationComplete(true), 0);
  };

  //Un champion est tout juste, assignement de la fonction à appelé
  const upgradeChampion = (champion) => {
    setOperationComplete(false);
    const isChampionInList = championListGuessRightOneTime.some(
      (item) => item.name === champion.name && item.id === champion.id
    );

    if (isChampionInList) {
      deleteChampionsPerfectTwice(champion);
    } else {
      addChampionsPerfectOnce(champion);
    }
  };

  //Un champion est faux, vérification de si il a été trouvé tout juste une fois
  const retrogradeChampion = (champion) => {
    const isChampionInList = championListGuessRightOneTime.some(
      (item) => item.name === champion.name && item.id === champion.id
    );

    if (isChampionInList) {
      setChampionListNotGuessYet((prevList) => [...prevList, champion]);
      setChampionListGuessRightOneTime((prevList) =>
        prevList.filter((item) => item.id !== champion.id)
      );
    }
  };

  return {
    championList,
    championId,
    championName,
    championCooldown,
    IsLoaded,
    championListNotGuessYet,
    championListGuessRightOneTime,
    operationComplete,
    setNewChampion,
    setRandomChampion,
    setTheEndOfLoading,
    upgradeChampion,
    retrogradeChampion,
  };
}

export default useChampionManagement;
