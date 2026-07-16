'use strict'

export default class Preload {
  constructor() {
    this.config = {

    }
    this.preloadLayer = document.querySelector('.preload')
  }

  preload () {
    console.log('preload');
    

    const cbStart = () => {

    }
    const cbComplete = () => {
      document.body.classList.add('loaded')
    }

    let tl = new gsap.timeline({paused: false})
    tl.fromTo('.preload-title', 1.5, { autoAlpha: 0, y: 40, beforeStart:cbStart}, { autoAlpha: 1, y: 0, ease: Power4.easeOut },'+=1.5');
    tl.to('.preload',.8,{height:0+'px',delay:2})
    tl.set('.preload',{display:'none',onComplete:cbComplete})

  }

  init() {
    if (this.preloadLayer) {
        this.preload()   
    }

  }


}
