import "./bootstrap";
import Lenis from "lenis";
import "lenis/dist/lenis.css"; // Optional: if you want to use default CSS

// Initialize Lenis
const lenis = new Lenis({
    autoRaf: true, // Automatically handle requestAnimationFrame
});

// Listen for the scroll event
lenis.on("scroll", (e) => {
    console.log(e);
});
