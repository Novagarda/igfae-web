'use strict'

export default class Scroll {
    constructor() {
        this.config = {
        }

    }

    smootherHandler() {
        ScrollSmoother.create({
            smooth: 1,
            effects: true,
        });
    }

    intro() {

        let tl = new gsap.timeline({ paused: false })
        tl.fromTo(
            '.home-hero-logo span',
            {
                opacity: 0.1,
                y: '15%'
            },
            {
                opacity: 1,
                stagger: 0.5,
                duration: 2,
                y: 0
            }
        )
        // tl.fromTo(
        //   '.superbanner',
        //   {
        //     opacity: 0.1,
        //     y: '15%'
        //   },
        //   {
        //       opacity: 1,
        //       duration: 2,
        //       y: 0
        //   }
        // )


        // this.gsapTimelines.push(
        //   gsap
        //       .timeline({
        //           scrollTrigger: {
        //               trigger: 'body',
        //               start: 'top top',
        //               end: 'bottom bottom',
        //               duration: 4,
        //               scrub: true,
        //               toggleActions: 'play reverse play reverse', // Controla la animación cuando se activa y desactiva
        //             },
        //       })
        //       .fromTo(
        //         '.home-hero-logo span',
        //         {
        //           opacity: 0.1,
        //           y: '15%'
        //         },
        //         {
        //             opacity: 1,
        //             stagger: 0.5,
        //             duration: 1,
        //             y: 0
        //         }
        //       )
        // );
    }

    superfeatured() {
        this.gsapTimelines.push(
            gsap
                .timeline({
                    scrollTrigger: {
                        trigger: '.superfeatured-block',
                        start: 'top 50%',
                        end: 'bottom bottom',
                        duration: 4,
                        scrub: true,
                        toggleActions: 'play reverse play reverse', // Controla la animación cuando se activa y desactiva
                    },
                })
                .fromTo(
                    '.superfeatured-block',
                    {
                        scaleX: '100%',

                    },
                    {
                        scaleX: '105%',
                        duration: 2,
                        ease: 'Power2.easeOut' // Añade ease para suavizar la animación
                    }
                )
        );
    }

    superfeaturedItems() {
        // Selecciona todos los elementos .superfeatured-item
        const triggers = document.querySelectorAll('.superfeatured-item');

        triggers.forEach(trigger => {
            const target = trigger.querySelector('.superfeatured-info'); // Busca el .superfeatured-info dentro de cada .superfeatured-item

            if (target) {
                this.gsapTimelines.push(
                    gsap
                        .timeline({
                            scrollTrigger: {
                                trigger: trigger, // Usa .superfeatured-item como disparador
                                start: 'top top',
                                end: 'bottom bottom',
                                toggleActions: 'play play reverse none',
                            },
                        })
                        .fromTo(
                            target, // Usa el .superfeatured-info como el elemento a animar
                            {
                                opacity: 0,
                                y: 2,
                            },
                            {
                                opacity: 1,
                                y: 0,
                                ease: 'Power2.easeOut' // Añade ease para suavizar la animación

                            }
                        )
                );
            }
        });
    }

    init() {
        if (this.initialized) {
            return;
        }

        ScrollTrigger.refresh()

        this.gsapTimelines = [];
        this.initialized = true;

        // this.intro()
        // this.superfeatured()
        // this.superfeaturedItems()
        this.smootherHandler()
    }
}
