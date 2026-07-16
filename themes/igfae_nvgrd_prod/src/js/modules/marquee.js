'use strict'

export default class Marquee {
  constructor () {
    this.Elem = document.querySelector('.marquee');
    this.NumOfElements = 1;
    if(this.Elem){
      this.NumOfElements = this.Elem.dataset.length;
    }
    
    this.config = {
      itemsClass: 'marquee-link',
      numberOfClones: this.NumOfElements > 1 ? 5 : 10
    }
  }

  handlerItem () {
    document.querySelectorAll('.' + this.config.itemsClass).forEach((item, i) => {
      item.classList.add('parsed');
      for (i = 0; i < this.config.numberOfClones; i++) {
        const clone = item.cloneNode(true);
        clone.classList.add(this.config.itemsClass + '--clone');
        item.after(clone);
      }
    });
  }

  init () {
    if (this.Elem) {
      this.handlerItem()
    }
  }
}
