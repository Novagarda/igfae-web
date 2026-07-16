'use strict'

import Plyr from 'plyr';
import 'plyr/dist/plyr.css'

export default class Video {
  constructor() {
    this.config = {

    }
    this.videos = document.querySelectorAll('.video-wrapper')
  }

  handlerVideo() {
    this.videos.forEach(item => {
      const player = new Plyr(item.querySelector('.player'),
        {
          autoplay: true,
          muted: true,
          loop: { active: true },
          playsinline: true,
          controls: ['play-large', 'play', 'progress', 'mute', 'fullscreen', 'captions'],
        });
      item.plyr = player
      item.plyr.play()

      // Intersection Observer
      const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            item.plyr.play(); // Reproduce el video cuando entra en el viewport
          } else {
            item.plyr.pause(); // Pausa el video cuando sale del viewport
          }
        });
      }, {
        threshold: 0.5 // El video se reproducirá cuando el 50% esté visible
      });

      observer.observe(item); // Observa el video
    });
  }

  init() {
    if (this.videos) {
      this.handlerVideo()
    }
  }

}
