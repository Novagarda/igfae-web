'use strict'

export default class Lazy {
  constructor() {
    this.config = {

    }

  }

  loadLazyImages(image) {

    var getSiblings = function (elem) {

      // Setup siblings array and get the first sibling
      var siblings = [];
      var sibling = elem.parentNode.firstChild;

      // Loop through each sibling and push to the array
      while (sibling) {
        if (sibling.nodeType === 1 && sibling !== elem) {
          siblings.push(sibling);
        }
        sibling = sibling.nextSibling
      }

      return siblings;

    };


    // Está cargando la imagen 2 vces....
    // Creo que es por la opción de no usar cache de las developer tools

    // Con el evento load funciona bien. Con el swup y sus eventos no

    function loadImage(url) {
      return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => resolve(url);
        img.onerror = reject;
        img.src = url;
      });
    }

    if (!image.classList.contains('loaded')) {
      const src = image.getAttribute('data-src');
      loadImage(src).then(() => {
        // console.log('loaded',image);
        image.setAttribute('src', src);
        image.classList.add('loaded');

      });
    }

    var siblings = getSiblings(image);
    siblings.forEach((item, i) => {
      item.srcset = item.getAttribute('data-srcset')
      item.classList.add('loaded')
    })

  }

  checkImagesViewport() {
    const images = document.querySelectorAll('.lazy[data-src]')

    images.forEach(element => {
      var observer = new IntersectionObserver((entries) => {
        if (!entries[0].isIntersecting) {

        }
        else {
          this.loadLazyImages(element)
        }
      });

      observer.observe(element)
    });


  }

  imagesAnimation() {
    const images = document.querySelectorAll('.image img');
    const canvas = document.querySelector('canvas');

    if (!canvas) {
      console.error("No se encontró el elemento <canvas> con la clase 'pixel-canvas'.");
      return;
    }

    const context = canvas.getContext('2d');
    if (!context) {
      console.error("No se pudo obtener el contexto 2D del canvas.");
      return;
    }

    const pixelSize = 6; // Tamaño de pixelado inicial
    let currentPixelSize = 0; // Tamaño de píxel inicial para la animación

    images.forEach(image => {
      image.addEventListener('mouseenter', () => {
        const rect = image.getBoundingClientRect();
        canvas.width = rect.width;
        canvas.height = rect.height;
        canvas.style.left = `${rect.left + window.scrollX}px`;
        canvas.style.top = `${rect.top + window.scrollY}px`;
        canvas.style.opacity = 1; // Mostrar el canvas al hacer hover

        // Dibuja la imagen en el canvas
        context.clearRect(0, 0, canvas.width, canvas.height);
        context.drawImage(image, 0, 0, rect.width, rect.height);

        // Animación de transición de píxeles
        const pixelTransition = setInterval(() => {
          if (currentPixelSize < pixelSize) {
            currentPixelSize += 1; // Aumenta suavemente el tamaño del píxel
            // Aplica el efecto de pixelado
            for (let y = 0; y < canvas.height; y += currentPixelSize) {
              for (let x = 0; x < canvas.width; x += currentPixelSize) {
                const imageData = context.getImageData(x, y, currentPixelSize, currentPixelSize);
                const color = imageData.data;
                context.fillStyle = `rgb(${color[0]}, ${color[1]}, ${color[2]})`;
                context.fillRect(x, y, currentPixelSize, currentPixelSize);
              }
            }
          } else {
            clearInterval(pixelTransition); // Detiene la animación cuando se alcanza el tamaño de píxel final
          }
        }, 10); // Controla la velocidad de la animación
      });

      image.addEventListener('mouseleave', () => {
        // Oculta el canvas cuando el cursor sale de la imagen
        canvas.style.opacity = 0;
        currentPixelSize = 0; // Reinicia el tamaño de píxel para la próxima vez
      });
    });
  }



  checkResize() {
    window.addEventListener('resize', () => { this.checkSection() })
  }

  init() {
    this.checkImagesViewport()
    // this.imagesAnimation()

    // this.checkResize()
  }
}
