'use strict'

// core version + navigation, pagination modules:
import Swiper, { Navigation, Pagination, Lazy, Autoplay } from 'swiper';
// import Swiper and modules styles
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

Swiper.use([Navigation]);

export default class Slider {
  constructor() {
    this.config = {
    }
    this.Elem = document.querySelectorAll('.swiper');
  }

  slider() {

    this.Elem.forEach((item, i) => {

      // Resolve the slide count using a defensive strategy to support all template variants:
      // - `data-length`: canonical attribute name used by current templates.
      // - `data-lenght`: legacy typo kept for backward compatibility.
      // - DOM fallback: real-time count of `.swiper-slide` elements when no data attribute is provided
      //   (this is especially relevant for the Home slider markup).

      const numImaxes = Number(
        item.dataset.length ||
        item.dataset.lenght ||
        item.querySelectorAll('.swiper-slide').length ||
        0
      );
      const isHomeSlider = Boolean(item.closest('.home-swiper'));

      const sliderItem = new Swiper(item, {
        slidesPerView: "auto",
        spaceBetween: 18,
        // Loop policy:
        // - Home slider (`.home-swiper`) always loops, independent of slide count.
        // - All other sliders loop only when they contain more than 3 slides.
        // This preserves the expected UX in Home while avoiding unnecessary loop behavior in
        // shorter galleries across the rest of the site.
        loop: isHomeSlider || numImaxes > 3,
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
      });
    });
  }

  init() {

    if (this.Elem) {
      this.slider()
    }

  }
}
