import PropTypes from "prop-types";
import { useEffect } from "react";

//Historique des bonnes et mauvaises réponses
function BoxScore({
  score,
  history,
  upgradeChampion,
  retrogradeChampion,
  setRandomChampion,
  operationComplete,
}) {
  //UseEffect se déclenchant lorsque quelque chose est ajouté dans l'historique
  useEffect(() => {
    if (history.length > 0) {
      const lastEntry = history[history.length - 1]; // Récupère la dernière entrée
      const allCorrect = lastEntry.results.every((result) => result === true); // Vérifie si tous les résultats sont vrais

      const processHistoryUpdate = async () => {
        if (allCorrect) {
          upgradeChampion({ name: lastEntry.name, id: lastEntry.icon }); // Appelle upgradeChampion
        } else {
          retrogradeChampion({ name: lastEntry.name, id: lastEntry.icon }); // Appelle retrogradeChampion
        }
      };

      processHistoryUpdate(); // Appelle la fonction asynchrone pour gérer l'historique
    }
  }, [history]);

  //UseEffect de relance d'un nouveau champion quand toutes les données sont bien ajoutés dans l'historique
  useEffect(() => {
    if (operationComplete) {
      setRandomChampion();
    }
  }, [operationComplete]);

  return (
    <div
      id="boxScore"
      style={{
        padding: "15px",
        backgroundColor: "#1e1e1e",
        borderRadius: "12px",
        color: "#ddd",
        maxWidth: "500px",
        boxShadow: "0 4px 10px rgba(0, 0, 0, 0.6)",
        fontFamily: "'Arial', sans-serif",
        overflow: "scroll",
        maxHeight: "600px",
      }}
    >
      <h3 style={{ color: "rgb(175, 76, 76)" }}>Perfect Streak : {score}</h3>

      <ul
        style={{
          listStyle: "none",
          padding: "0",
          display: "flex",
          order: "revert",
          flexDirection: "column-reverse",
        }}
      >
        {history.map((entry, index) => (
          <li
            key={index}
            style={{
              display: "flex",
              alignItems: "center",
              gap: "10px",
              marginBottom: "10px",
              borderBottom: "1px solid #444",
              paddingBottom: "5px",
            }}
          >
            <img
              src={`https://ddragon.leagueoflegends.com/cdn/14.24.1/img/champion/${entry.icon}.png`}
              alt={entry.name}
              style={{ width: "40px", height: "40px", borderRadius: "50%" }}
            />
            <span style={{ flex: 1 }}>{entry.name}</span>
            {entry.results.map((result, i) => (
              <span
                key={i}
                style={{
                  color: result ? "green" : "red",
                  fontWeight: "bold",
                  fontSize: "18px",
                }}
              >
                {result ? "✔" : "✘"}
              </span>
            ))}
          </li>
        ))}
      </ul>
    </div>
  );
}

BoxScore.propTypes = {
  score: PropTypes.number.isRequired,
  history: PropTypes.arrayOf(
    PropTypes.shape({
      icon: PropTypes.string.isRequired,
      name: PropTypes.string.isRequired,
      results: PropTypes.arrayOf(PropTypes.bool).isRequired,
    })
  ).isRequired,
  upgradeChampion: PropTypes.func,
  retrogradeChampion: PropTypes.func,
  setRandomChampion: PropTypes.func,
  operationComplete: PropTypes.bool,
};

export default BoxScore;
