import "./bootstrap";
import "flowbite";

import { Carousel } from "flowbite";

const carouselElement = document.getElementById("default-carousel");
const items = [
    {
        position: 0,
        el: document.getElementById("carousel-item-1"),
    },
    {
        position: 1,
        el: document.getElementById("carousel-item-2"),
    },
    {
        position: 2,
        el: document.getElementById("carousel-item-3"),
    },
];

// options with default values
const options = {
    defaultPosition: 0,

    indicators: {
        activeClasses: "bg-white dark:bg-gray-800",
        inactiveClasses:
            "bg-white/50 dark:bg-gray-800/50 hover:bg-white dark:hover:bg-gray-800",
    },

    // callback functions
    onChange: () => {},
};

// instance options object
const instanceOptions = {
    id: "default-carousel",
    override: true,
};

const carousel = new Carousel(carouselElement, items, options, instanceOptions);

function toggleInactiveClass() {
    items.forEach((item) => {
        if (item.position !== carousel.getActiveItem().position) {
            item.el.classList.add("scale-75");
        } else {
            item.el.classList.remove("scale-75");
        }
    });
}
