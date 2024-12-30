import PropTypes from "prop-types";

//Logo du site
function Logo({ center = false }) {
  return (
    <div
      id="LogoDiv"
      style={{
        position: "absolute",
        top: center ? "50%" : "20px",
        left: center ? "50%" : "20px",
        transform: center ? "translate(-50%, -50%)" : "none",
        lineHeight: "0.9",
        fontFamily: "Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif",
        textTransform: "uppercase",
        color: "#333",
        textShadow:
          "rgb(175, 76, 76) 3px 3px 0px, rgba(0, 0, 0, 0.5) 6px 6px 0px",
        letterSpacing: "2px",
        zIndex: "1",
        WebkitTextStroke: "1px rgb(144, 98, 98)",
      }}
    >
      <div style={{ fontSize: "44px" }}>Guess The</div>
      <div style={{ fontSize: "44px" }}>Ultimate</div>
      <div style={{ fontSize: "44px" }}>Cooldowns</div>
    </div>
  );
}

Logo.propTypes = {
  center: PropTypes.bool,
};

export default Logo;
