import { useEffect, useRef, useState } from "react";
import Logo from "../Components/Logo";
import AutocompleteField from "../Components/AutocompleteField";
import BoxScore from "../Components/BoxScore";
import useChampionManagement from "../hooks/useChampionManagement";

//Page du site
function Guess() {
  const {
    championId,
    championName,
    championCooldown,
    championList,
    IsLoaded,
    operationComplete,
    setNewChampion,
    setRandomChampion,
    setTheEndOfLoading,
    upgradeChampion,
    retrogradeChampion,
  } = useChampionManagement();

  const [inputs, setInputs] = useState(["", "", ""]); // Valeurs des inputs
  const [revealedAnswers, setRevealedAnswers] = useState(["", "", ""]); // Réponses révélées
  const inputRefs = [useRef(null), useRef(null), useRef(null)]; //Références des inputs
  const [inputBorders, setInputBorders] = useState(["", "", ""]); // Couleurs des bordures des inputs
  const [score, setScore] = useState(0); // Score total
  const [history, setHistory] = useState([]); // Historique des résultats
  const [validationMessage, setValidationMessage] = useState(""); // État pour le message de validation

  //Reset des inputs au changement de champion
  useEffect(() => {
    setInputs(["", "", ""]);
    setInputBorders(["", "", ""]);
    setRevealedAnswers(["", "", ""]);
  }, [championId]);

  //Changement de focus lorsque les donéées sont chargés
  useEffect(() => {
    if (inputRefs[0].current) {
      inputRefs[0].current.focus();
    }
  }, [IsLoaded]);

  // Met à jour les inputs en fonction des saisies
  const handleInputChange = (index, value) => {
    const newInputs = [...inputs];
    newInputs[index] = value;
    setInputs(newInputs);
  };

  // Vérifie si la réponse saisie est correcte
  const isCorrect = (index) => {
    return (
      inputs[index] === String(championCooldown[index]) &&
      revealedAnswers[index] === ""
    );
  };

  // Révèle la réponse correcte au clic
  const revealAnswer = () => {
    const newRevealedAnswers = championCooldown;
    setRevealedAnswers(newRevealedAnswers);
  };

  //Fonction validant les ou non les réponses dans les inputs
  const validateInputs = () => {
    //Cas où tous les champs sont correctement remplis
    if (inputs[0] !== "" && inputs[1] !== "" && inputs[2] !== "") {
      setValidationMessage("");
      const allCorrect = inputs.every((value, i) => isCorrect(i));

      // Mise à jour des couleurs des bordures
      const newBorders = inputs.map((_, i) => {
        if (revealedAnswers[i] !== "") return "orange"; // Réponse révélée
        return isCorrect(i) ? "green" : "red"; // Correct : vert, incorrect : rouge
      });
      setInputBorders(newBorders);
      //Si toutes les réponses sont juste
      if (allCorrect) {
        setScore((prevScore) => prevScore + 1);
        inputRefs[0].current.focus();
      }
      //Si les réponses ont été dévoilées
      else if (revealedAnswers[0] !== "") {
        setScore(0);
        setRandomChampion();
      }
      //Si les réponses sont fausses
      else {
        setScore(0);
        revealAnswer();
      }
      //Si l'historique n'est pas vide, vérification pour éviter les doublons
      if (history.length > 0) {
        let lenght = history.length - 1;
        if (
          history[lenght].icon === championId &&
          isCorrect(0) === false &&
          isCorrect(1) === false &&
          isCorrect(2) === false
        ) {
          setHistory(history);
        }
        //Ajout à l'historique si l'historique est vide
        else {
          setHistory((prevHistory) => [
            ...prevHistory,
            {
              icon: championId,
              name: championName,
              results: inputs.map((_, i) => isCorrect(i)),
            },
          ]);
        }
      }
      // Cas où les réponses ne sont pas juste
      else {
        setHistory((prevHistory) => [
          ...prevHistory,
          {
            icon: championId,
            name: championName,
            results: inputs.map((_, i) => isCorrect(i)),
          },
        ]);
      }

      // Réinitialisation des champs inputRefs et inputs
      inputRefs.forEach((ref) => {
        if (ref.current) {
          ref.current.value = ""; // Vide directement l'input DOM
        }
      });
    }
    //Cas où tous les champs ne sont pas remplis
    else {
      setValidationMessage("Fill all the inputs."); // Définit le message d'erreur
    }
  };

  // Gère la touche Entrée pour déplacer le focus
  const handleEnterKey = (e, index) => {
    if (e.key === "Enter") {
      e.preventDefault();

      // Si tous les inputs sont remplis, valider les réponses
      if (inputs[0] !== "" && inputs[1] !== "" && inputs[2] !== "") {
        validateInputs();
      }
      // Sinon, déplacer le focus vers le champ suivant
      else if (inputRefs[index + 1] && inputRefs[index + 1].current) {
        inputRefs[index + 1].current.focus();
      }
      // Vérification si tous les inputs sont remplis lors de l'appui sur a touche entrée dans le dernier input
      else if (index === 2) {
        inputRefs[0].current.focus();
      }
    }
  };
  //Gestion de l'appui sur les touches Tab et flèche du haut et du bas
  const handleNavigationKey = (e, index) => {
    const isShiftTab = e.key === "Tab" && e.shiftKey;
    const isTab = e.key === "Tab" && !e.shiftKey;
    const isArrowDown = e.key === "ArrowDown";
    const isArrowUp = e.key === "ArrowUp";
    //Cas Tab ou Flèche du bas
    if (isTab || isArrowDown) {
      e.preventDefault();
      const nextIndex = (index + 1) % inputRefs.length;
      inputRefs[nextIndex].current.focus();
    }
    //Cas Shift+Tab et flèche du haut
    else if (isShiftTab || isArrowUp) {
      e.preventDefault();
      const prevIndex = (index - 1 + inputRefs.length) % inputRefs.length;
      inputRefs[prevIndex].current.focus();
    }
  };

  //Gestion des caractères autorisés dans les inputs
  const handleOtherThanNumberAndBackSpace = (e) => {
    //Refus de tous les caractères autre que "Suppression", "Point" et autres choses que des nombres
    if (
      !(
        e.key === "Backspace" ||
        e.key === "Delete" ||
        e.key === "Period" ||
        e.keyCode === 8 ||
        e.keyCode === 46 ||
        e.keyCode === 59 ||
        e.keyCode === 110 ||
        e.keyCode === 190 ||
        /[0-9]/.test(e.key)
      )
    ) {
      e.preventDefault();
    }
  };

  return (
    <>
      <Logo />
      <div
        style={{
          display: "flex",
          alignItems: "center",
          width: "98vw",
          flexDirection: "column",
        }}
      >
        <AutocompleteField
          championList={championList}
          setNewChampion={setNewChampion}
          setRandomChampion={setRandomChampion}
        />
        <div
          id="mainContent"
          style={{ display: "flex", gap: "20px", height: "80vh" }}
        >
          <div
            id="championDiv"
            style={{
              display: "flex",
              flexDirection: "column",
              alignItems: "center",
              justifyContent: "center",
              marginTop: "20px",
              padding: "20px",
              borderRadius: "12px",
              margin: "auto",
              backgroundColor: "#1e1e1e",
              fontFamily: "'Arial', sans-serif",
              width: "500px",
              boxShadow: "0 4px 10px rgba(0, 0, 0, 0.6)",
            }}
          >
            <h2
              style={{
                fontSize: "42px",
                marginBottom: "10px",
                color: "rgb(175, 76, 76)",
              }}
            >
              {IsLoaded ? championName : "Loading..."}
            </h2>
            <img
              src={`https://ddragon.leagueoflegends.com/cdn/img/champion/splash/${championId}_0.jpg`}
              alt={`${championName} splash art`}
              style={{
                width: "100%",
                borderRadius: "12px",
                border: "2px solid rgb(175, 76, 76)",
                boxShadow: "0 4px 8px rgba(0, 0, 0, 0.7)",
                marginBottom: "20px",
                transition: "filter 0.3s ease, opacity 0.3s ease",
                filter: IsLoaded ? "none" : "blur(10px)", // Applique un flou si non chargé
                opacity: IsLoaded ? 1 : 0.5, // Réduit l'opacité si non chargé
                backgroundColor: "rgb(175, 76, 76)",
              }}
              onLoad={() => {
                setTheEndOfLoading();
              }}
            />
            {["Rank 1", "Rank 2", "Rank 3"].map((label, index) => (
              <div key={index} style={{ marginBottom: "15px", width: "100%" }}>
                <label
                  style={{
                    display: "block",
                    marginBottom: "5px",
                    fontWeight: "bold",
                    color: "#ddd",
                  }}
                >
                  {label} :
                </label>
                <div
                  style={{
                    display: "flex",
                    alignItems: "center",
                    flexWrap: "wrap",
                    gap: "10px",
                  }}
                >
                  <input
                    ref={inputRefs[index]}
                    type="text"
                    value={inputs[index]}
                    disabled={!IsLoaded}
                    onChange={(e) => handleInputChange(index, e.target.value)}
                    onKeyDown={(e) => {
                      handleOtherThanNumberAndBackSpace(e);
                      handleEnterKey(e, index);
                      handleNavigationKey(e, index);
                    }}
                    style={{
                      flex: "1 1 auto",
                      padding: "10px",
                      borderRadius: "8px",
                      border: `2px solid ${inputBorders[index] || "rgb(175, 76, 76)"}`, // Couleur dynamique
                      outline: "none",
                      fontSize: "16px",
                      backgroundColor: IsLoaded ? "#242424" : "black",
                      color: "#fff",
                      boxShadow: "0 4px 8px rgba(0, 0, 0, 0.6)",
                    }}
                  />
                  <h4 style={{ color: "#ddd", margin: "0" }}>
                    {revealedAnswers[index] === ""
                      ? ""
                      : `${revealedAnswers[index]} seconds`}
                  </h4>
                </div>
              </div>
            ))}
            <div style={{ display: "flex", gap: "20px", flexDirection: "row" }}>
              <button
                onClick={() => validateInputs()}
                disabled={!IsLoaded}
                style={{
                  padding: "10px",
                  backgroundColor: IsLoaded ? "rgb(175, 76, 76)" : "#661f1f",
                  color: "#fff",
                  border: "none",
                  borderRadius: "8px",
                  cursor: "pointer",
                  boxShadow: "0 4px 8px rgba(0, 0, 0, 0.6)",
                  whiteSpace: "nowrap",
                }}
              >
                Submit
              </button>
              <button
                onClick={() => revealAnswer()}
                disabled={!IsLoaded}
                style={{
                  padding: "10px",
                  backgroundColor: IsLoaded ? "rgb(175, 76, 76)" : "#661f1f",
                  color: "#fff",
                  border: "none",
                  borderRadius: "8px",
                  cursor: "pointer",
                  boxShadow: "0 4px 8px rgba(0, 0, 0, 0.6)",
                  whiteSpace: "nowrap",
                }}
              >
                Show the answers
              </button>
            </div>
            {validationMessage && (
              <p style={{ color: "red", marginTop: "10px", fontSize: "14px" }}>
                {validationMessage}
              </p>
            )}
          </div>
          <BoxScore
            score={score}
            history={history}
            upgradeChampion={upgradeChampion}
            retrogradeChampion={retrogradeChampion}
            setRandomChampion={setRandomChampion}
            operationComplete={operationComplete}
          />
        </div>
      </div>
    </>
  );
}

export default Guess;
