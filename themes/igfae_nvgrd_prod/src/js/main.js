/* scripts/main.js */

'use strict'

import {
  gsap,
  ScrollTrigger,
  Power4,
  Linear
} from "gsap/all";

// Asigna gsap y Power4 al objeto global (window en el navegador)
window.gsap = gsap;
window.ScrollTrigger = ScrollTrigger;
window.Power4 = Power4;
window.Linear = Linear;

// Exporta los módulos que necesitas
export {
  gsap,
  ScrollTrigger,
  Power4,
  Linear
};

gsap.registerPlugin(ScrollTrigger)

import viewportUnitsBuggyfill from 'viewport-units-buggyfill'
import Debug from './modules/debug'
import Checks from './modules/checks'
import Lazy from './modules/lazy'
import PageLoader from './modules/loader'
import Marquee from './modules/marquee'
import Nav from './modules/nav'
import Effects from './modules/effects'
import Events from './modules/events'
import Igfae from './modules/igfae'
import Slider from './modules/slider'
import Preload from './modules/preload'
import Video from './modules/video'

viewportUnitsBuggyfill.init()

const IGFAE = new Igfae();

const initFun = () => {
  new Debug().init()
  new Checks().init()
  new PageLoader().init()
  new Preload().init()
  new Marquee().init()
  new Nav().init()
  new Lazy().init()
  new Events().init()
  new Effects().init()
  IGFAE.init()
  IGFAE.once()
  new Slider().init()
  new Video().init()
}


function documentReady(fn) {
  if (document.readyState !== 'loading') {
    fn()
  } else if (document.addEventListener) {
    document.addEventListener('DOMContentLoaded', fn)
    console.log('Ready!')
  } else {
    document.attachEvent('onreadystatechange', function () {
      if (document.readyState !== 'loading') {
        fn()
      }
    })
  }
}
documentReady(initFun)

document.addEventListener('swup:page:view', (event) => {
  new Lazy().init()
  new Events().recarga()
  new Effects().init()
  IGFAE.init()
  new Slider().init()
  new Video().init()
  new Marquee().init()
  new Nav().recarga()
});

document.addEventListener('swup:visit:start', (event) => {
  IGFAE.limparAoInicio()
});

document.addEventListener('swup:content:replace', (event) => {
  IGFAE.limparAoFinal()
});