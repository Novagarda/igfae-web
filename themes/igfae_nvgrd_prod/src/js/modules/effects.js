'use strict'

export default class Effects {
  constructor() {
    this.config = {
    }
  }

  inicia() {

    // REVEAL
    // Collido de: https://www.youtube.com/watch?app=desktop&v=VplDlwLTR50
    reveal();
    window.addEventListener('scroll', reveal);

    function reveal(){
      var reveals = document.querySelectorAll('.reveal');

      reveals.forEach(reveal => {
        var windowHeight = window.innerHeight;
        var revealtop = reveal.getBoundingClientRect().top;
        var revealpoint = 60;
        
        if(revealtop < windowHeight - revealpoint){
          reveal.classList.add('revealed');
        }else{
          reveal.classList.remove('revealed');
        }
        
      });
    }
  }

  init() {
    this.inicia()
  }
}
