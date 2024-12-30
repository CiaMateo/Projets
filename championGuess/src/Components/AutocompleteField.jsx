import { useEffect, useState, useRef } from "react";
import PropTypes from "prop-types";

//Champ de sélection manuelle du personnage + bouton aléatoire
function AutocompleteField({
  championList,
  setNewChampion,
  setRandomChampion,
}) {
  const [filteredChampions, setFilteredChampions] = useState([]); // Suggestions filtrées
  const [inputValue, setInputValue] = useState(""); // Valeur du champ de texte
  const [showSuggestions, setShowSuggestions] = useState(false); // Affiche les suggestions
  const [hoveredChampion, setHoveredChampion] = useState(null); // Champion survolé
  const suggestionsRef = useRef(null);

  // Filtre les suggestions en fonction de l'input
  const handleInputChange = (e) => {
    const value = e.target.value;
    setInputValue(value);
    if (value) {
      const filtered = championList.filter((champ) =>
        champ.name.toLowerCase().includes(value.toLowerCase())
      );
      setFilteredChampions(filtered);
      setShowSuggestions(true);
    } else {
      setShowSuggestions(false);
    }
  };

  // Gère la sélection d'un champion
  const handleSelectChampion = (champion) => {
    setInputValue(champion.name);
    setNewChampion(champion);
    setShowSuggestions(false);
    setHoveredChampion(null);
  };

  // Ferme les suggestions si on clique en dehors
  useEffect(() => {
    const handleClickOutside = (e) => {
      if (
        suggestionsRef.current &&
        !suggestionsRef.current.contains(e.target)
      ) {
        setShowSuggestions(false);
      }
    };
    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, []);

  // Gère la touche Entrée
  const handleKeyDown = (e) => {
    // Priorité au champion survolé, sinon le premier de la liste
    if (e.key === "Enter") {
      e.preventDefault();
      const selectedChampion = hoveredChampion || filteredChampions[0];
      if (selectedChampion) {
        handleSelectChampion(selectedChampion);
      }
    }
  };

  return (
    <div
      id="ResearchComponentMainDiv"
      style={{ paddingBottom: "20px", width: "700px" }}
      onKeyDown={handleKeyDown} // Ajoute l'écoute pour la touche Entrée
    >
      <label
        style={{
          display: "block",
          marginBottom: "10px",
          fontWeight: "bold",
          color: "#fff",
        }}
      >
        Select a champion :
      </label>
      <div
        style={{
          position: "relative",
          display: "flex",
          flexWrap: "wrap",
          gap: "20px",
        }}
      >
        <input
          type="text"
          value={inputValue}
          onChange={handleInputChange} //Gestion du changement de la valeur
          placeholder="Search a champion..."
          style={{
            flex: "2 1 200px",
            borderRadius: "8px",
            border: "2px solid rgb(175, 76, 76)",
            fontSize: "16px",
            color: "#fff",
            backgroundColor: "#302c34",
            outline: "none",
            boxShadow: "0 4px 8px rgba(0, 0, 0, 0.6)",
          }}
        />
        {showSuggestions && (
          <ul
            ref={suggestionsRef}
            style={{
              position: "absolute",
              top: "100%",
              left: 0,
              width: "100%",
              maxHeight: "200px",
              overflowY: "auto",
              marginTop: "5px",
              padding: "0",
              listStyle: "none",
              backgroundColor: "#302c34",
              border: "1px solid rgb(175, 76, 76)",
              borderRadius: "8px",
              zIndex: 10,
            }}
          >
            {filteredChampions.map((champ) => (
              <li
                key={champ.id}
                onClick={() => handleSelectChampion(champ)} //Sélection du champion au clic
                onMouseOver={() => setHoveredChampion(champ)} // Met à jour le champion survolé
                onMouseOut={() => setHoveredChampion(null)} // Réinitialise lorsqu'on sort
                style={{
                  display: "flex",
                  alignItems: "center",
                  padding: "8px",
                  cursor: "pointer",
                  color: "#fff",
                  borderBottom: "1px solid #444",
                  backgroundColor:
                    hoveredChampion?.id === champ.id ? "#333" : "transparent",
                }}
              >
                <img
                  src={champ.splashArt}
                  alt={champ.name}
                  style={{
                    width: "40px",
                    height: "40px",
                    marginRight: "10px",
                    border: "1px solid rgb(175, 76, 76)",
                  }}
                />
                <span style={{ fontSize: "16px" }}>{champ.name}</span>
              </li>
            ))}
          </ul>
        )}
        <button
          onClick={() => setRandomChampion()} //Nouveau champion aléatoire
          style={{
            padding: "10px",
            backgroundColor: "rgb(175, 76, 76)",
            color: "#fff",
            border: "none",
            borderRadius: "8px",
            cursor: "pointer",
            boxShadow: "0 4px 8px rgba(0, 0, 0, 0.6)",
            whiteSpace: "nowrap",
          }}
        >
          Random
        </button>
      </div>
    </div>
  );
}

AutocompleteField.propTypes = {
  championList: PropTypes.array,
  setNewChampion: PropTypes.func,
  setRandomChampion: PropTypes.func,
};

export default AutocompleteField;
