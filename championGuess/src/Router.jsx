import { createBrowserRouter } from "react-router-dom";
import Guess from "./routes/Guess";

//Organisation du routage (très simple en l'occurence)
const router = createBrowserRouter([
  {
    path: "/",
    element: <Guess />,
  },
]);

export { router };
