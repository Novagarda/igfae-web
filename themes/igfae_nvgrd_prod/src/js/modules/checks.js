'use strict'

import Utils from './utils.js'

export default class Checks {
  constructor() {
    this.config = {
      BREAKPOINTS: false
    }
    this.Utils = new Utils

  }

  isJs () {
    console.log('is js');
    document.documentElement.classList.add('js');
  }

  hasTouchScreen () {
    const checkDevice = () => {
      console.log('resize');
      if (this.Utils.hasTouchScreen()) {
        document.documentElement.classList.add('is-touch');
        document.documentElement.classList.remove('no-touch');
      } else {
        document.documentElement.classList.add('no-touch');
        document.documentElement.classList.remove('is-touch');
      }
    }

    window.addEventListener('resize',checkDevice)
    checkDevice()
  }

  showBreakpoints () {
    if (this.config.BREAKPOINTS) {
      console.log('showing breakpoints');
      document.querySelector('body').classList.add('debug')
      document.querySelector('body').classList.add('show-breakpoints');

      const getWidth = () => {
        document.querySelector('body').setAttribute('data-width', window.innerWidth+'px');
      }

      window.addEventListener('resize',getWidth)
      getWidth()

    }
  }

  createDebugButton () {
    var el = document.documentElement;

    // create a p element for inserting in el
    var newEl = document.createElement('button');
    newEl.style.height = '.5rem'
    newEl.style.width = '.5rem'
    newEl.style.position = 'fixed'
    newEl.style.zIndex = '100000000'
    newEl.addEventListener('click',() => {
      document.querySelector('body').classList.toggle('debug')
     })
    // use the innerHTML property for inserting HTML content
    // or append a textNode to the p element
    // newEl.appendChild(document.createTextNode('Hello World!'));

    // append p as a new child to el
    el.appendChild(newEl);

    // same result with insertBefore()
    el.insertBefore(newEl, null);

    // use as second argument the child node you want to insert the new node before
    // example: prepend newEl as first child to el
    el.insertBefore(newEl, el.childNodes[0] || null);
  }

  init() {
    this.isJs()
    this.hasTouchScreen()
    this.showBreakpoints()
    this.createDebugButton()
  }
}
