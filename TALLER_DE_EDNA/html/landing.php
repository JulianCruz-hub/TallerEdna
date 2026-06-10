<?php
// Pagina principal del taller.
require_once __DIR__ . '/../php/db.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Taller de Costura de Edna - Especialistas en trajes para superhéroes y villanos. Diseño, confección y talleres profesionales.">
    <title>Taller de Costura Edna - Trajes para Superheroes</title>
    <link rel="stylesheet" href="../css/body.css">
    <link rel="stylesheet" href="../css/header.css?v=3">
    <link rel="stylesheet" href="../css/footer.css">

    <link rel="icon" type="image/png" href="../img/favicon-96x96.png" sizes="96x96" />
</head>

<body>
    <header>
        <div class="logo-container">
            <a href="landing.php">
                <img src="../img/st,small,507x507-pad,600x600,f8f8f8.jpg" alt="Logo Taller Edna" class="logo">
            </a>
            <div class="site-name">
                <span>Taller de Costura de Edna</span>
                <span>La sastre de Villanos y Superheroes</span>
            </div>
        </div>

        <nav class="menu_header">
            <ul>
                <li><a href="#historia">Historia</a></li>
                <li><a href="#servicios">Servicios</a></li>
                <li><a href="#clientes">Clientes</a></li>
            </ul>
        </nav>

        <div class="user-options">
            <a href="Login.html" class="btn-accion">Iniciar Sesión</a>
        </div>
    </header>

    <main>
        <section class="hero">
            <video class="hero-video" autoplay muted loop playsinline>
                <source src="../video/Home.mp4" type="video/mp4">
            </video>
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <h1>Trajes Profesionales <br>para<br>Superheroes y Villanos</h1>
                <p>Excelencia en diseño y confección. Más de 20 años creando atuendos que salvan el mundo.</p>
                <a href="Formu.html" class="cta-btn">Solicitar Cita</a>
            </div>
        </section>

        <hr>

        <section class="split-section" id="historia">
            <div class="image-half">
                <img src="../img/revis.jpg" alt="Nuestro taller">
            </div>
            <div class="text-half">
                <h2>Nuestra Historia</h2>
                <p>Fundado en 2004 por Edna Mode, reconocida diseñadora internacional, nuestro taller nació con una idea muy clara: crear prendas capaces de combinar elegancia, resistencia y funcionalidad sin renunciar a la personalidad de quien las lleva. Desde nuestros inicios hemos trabajado con una visión artesanal, cuidando cada detalle del patronaje, la selección de materiales y los acabados, para que cada traje sea tan espectacular como duradero.</p>
                <p>Con el paso de los años, el taller se ha convertido en un espacio de referencia para superhéroes, villanos y clientes que buscan diseños únicos. Nuestro equipo de costureros, diseñadores y especialistas trabaja de forma cercana en cada proyecto, adaptando cada prenda a las necesidades reales de movimiento, comodidad y estilo. Esa mezcla de creatividad, experiencia y atención personalizada es lo que nos permite ofrecer un resultado premium en cada encargo.</p>
            </div>
        </section>

        <hr>

        <section class="services-section" id="servicios">
            <div class="container">
                <h2>Nuestros Servicios</h2>
                <div class="services-grid">
                    <div class="service-card">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-ruler-icon lucide-pencil-ruler">
                            <path d="M13 7 8.7 2.7a2.41 2.41 0 0 0-3.4 0L2.7 5.3a2.41 2.41 0 0 0 0 3.4L7 13" />
                            <path d="m8 6 2-2" />
                            <path d="m18 16 2-2" />
                            <path d="m17 11 4.3 4.3c.94.94.94 2.46 0 3.4l-2.6 2.6c-.94.94-2.46.94-3.4 0L11 17" />
                            <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                            <path d="m15 5 4 4" />
                        </svg>
                        <h3>Diseño Personalizado</h3>
                        <p>Creación de trajes únicos adaptados a tus necesidades específicas, desde capas resistentes hasta disfraces funcionales.</p>
                    </div>
                    <div class="service-card">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-scissors-icon lucide-scissors">
                            <circle cx="6" cy="6" r="3" />
                            <path d="M8.12 8.12 12 12" />
                            <path d="M20 4 8.12 15.88" />
                            <circle cx="6" cy="18" r="3" />
                            <path d="M14.8 14.8 20 20" />
                        </svg>
                        <h3>Confección Profesional</h3>
                        <p>Utilizamos materiales de alta calidad y técnicas avanzadas para garantizar durabilidad y comodidad.</p>
                    </div>
                    <div class="service-card">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-graduation-cap-icon lucide-graduation-cap">
                            <path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z" />
                            <path d="M22 10v6" />
                            <path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5" />
                        </svg>
                        <h3>Talleres Educativos</h3>
                        <p>Aprende las técnicas de costura profesional con nuestros talleres especializados para aspirantes a diseñadores.</p>
                    </div>
                    <div class="service-card">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-wrench-icon lucide-wrench">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.106-3.105c.32-.322.863-.22.983.218a6 6 0 0 1-8.259 7.057l-7.91 7.91a1 1 0 0 1-2.999-3l7.91-7.91a6 6 0 0 1 7.057-8.259c.438.12.54.662.219.984z" />
                        </svg>
                        <h3>Mantenimiento y Reparación</h3>
                        <p>Servicio de reparación y mantenimiento para trajes desgastados por el uso intensivo en misiones.</p>
                    </div>
                    <div class="service-card">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-cpu-icon lucide-cpu">
                            <path d="M12 20v2" />
                            <path d="M12 2v2" />
                            <path d="M17 20v2" />
                            <path d="M17 2v2" />
                            <path d="M2 12h2" />
                            <path d="M2 17h2" />
                            <path d="M2 7h2" />
                            <path d="M20 12h2" />
                            <path d="M20 17h2" />
                            <path d="M20 7h2" />
                            <path d="M7 20v2" />
                            <path d="M7 2v2" />
                            <rect x="4" y="4" width="16" height="16" rx="2" />
                            <rect x="8" y="8" width="8" height="8" rx="1" />
                        </svg>
                        <h3>Optimización Tecnológica de Trajes</h3>
                        <p>Integración de sensores, materiales inteligentes, sistemas de comunicación y mejoras funcionales para potenciar el rendimiento en misiones de alto riesgo.</p>
                    </div>
                    <div class="service-card">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-icon lucide-eye">
                            <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <h3>Consultoría de Imagen Heroica</h3>
                        <p>Asesoría personalizada para definir tu identidad visual: colores, siluetas, materiales y estilo que refuercen tu presencia como héroe o villano profesional.</p>
                    </div>
                    <div class="service-card">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-wind-icon lucide-wind">
                            <path d="M12.8 19.6A2 2 0 1 0 14 16H2" />
                            <path d="M17.5 8a2.5 2.5 0 1 1 2 4H2" />
                            <path d="M9.8 4.4A2 2 0 1 1 11 8H2" />
                        </svg>
                        <h3>Diseño Aerodinámico Avanzado</h3>
                        <p>Creación de trajes optimizados para velocidad, vuelo, agilidad o sigilo, utilizando patrones especiales y tejidos de última generación.</p>
                    </div>
                    <div class="service-card">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-icon lucide-shield">
                            <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                        </svg>
                        <h3>Trajes Anticatástrofes</h3>
                        <p>Confección de atuendos resistentes a fuego, explosiones, impactos extremos, electricidad o condiciones climáticas severas, garantizando máxima protección.</p>
                    </div>
                    <div class="service-card">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles-icon lucide-sparkles">
                            <path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z" />
                            <path d="M20 2v4" />
                            <path d="M22 4h-4" />
                            <circle cx="4" cy="20" r="2" />
                        </svg>
                        <h3>Adaptación para Poderes Especiales</h3>
                        <p>Diseño y modificación de trajes para usuarios con habilidades únicas: elasticidad, invisibilidad, fuego, hielo, fuerza extrema o transformación corporal.</p>
                    </div>
                    <div class="service-card">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-minus-icon lucide-user-minus">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <line x1="22" x2="16" y1="11" y2="11" />
                        </svg>
                        <h3>Trajes de Camuflaje y Sigilo</h3>
                        <p>Confección de atuendos especializados en infiltración, misiones nocturnas y operaciones encubiertas. Incluyen materiales absorbentes de luz, patrones dinámicos y tecnología de reducción de ruido.</p>
                    </div>
                </div>
            </div>
        </section>

        <hr>

        <section class="split-section reverse" id="clientes">
            <div class="text-half">
                <h2>Cliente Satisfecho</h2>
                <p>Contamos con la confianza de los superhéroes y villanos más reconocidos. Nuestros trajes han sido protagonistas en innumerables misiones exitosas.</p>
                <p>"Edna Mode es la mejor. Sus trajes salvan vidas." - Mr. Incredible</p>
            </div>
            <div class="image-half">
                <img src="../img/inv.jpg" alt="Clientes satisfechos">
            </div>
            <div class="text-half">
                <h2>Cliente Satisfecho</h2>
                <p>Pese a que se lo pedí en negro, la muy bruja me lo hizo en rojo.</p>
                <p>"Edna Mode es de lo mejor. Su traje nunca evita que me disparen." - Deadpool</p>
            </div>
            <div class="image-half">
                <img src="../img/deed.jpg" alt="Clientes satisfechos">
            </div>
        </section>

        <hr>

        <section class="testimonials-section">
            <div class="container">
                <h2>Testimonios</h2>
                <div class="testimonials-grid">
                    <div class="flip-card">
                        <div class="flip-card-inner">
                            <div class="flip-card-front">
                                <div class="quote-icon">
                                    <img src="../img/muscle.png" alt="Icono de comilla">
                                </div>
                                <p>"Los trajes de Edna son increíbles. Resisten cualquier tirón."</p>
                                <cite>- Miembro de los Increíbles -</cite>
                            </div>
                            <div class="flip-card-back">
                                <img src="../img/new_Violet.jpg" alt="Clientes satisfechos">
                                <cite>Equipo Increíbles</cite>
                            </div>
                        </div>
                    </div>
                    <div class="flip-card">
                        <div class="flip-card-inner">
                            <div class="flip-card-front">
                                <div class="quote-icon">
                                    <img src="../img/villian.png" alt="Icono de comilla">
                                </div>
                                <p>"Profesionalismo y calidad excepcional. Recomiendo ampliamente."</p>
                                <cite>Villano reformado</cite>
                            </div>
                            <div class="flip-card-back">
                                <img src="../img/new_Syndrome.jpg" alt="Clientes satisfechos">
                                <cite>Sin equipo</cite>
                            </div>
                        </div>
                    </div>
                    <div class="flip-card">
                        <div class="flip-card-inner">
                            <div class="flip-card-front">
                                <div class="quote-icon">
                                    <img src="../img/muscle.png" alt="Icono de comilla">
                                </div>
                                <p>"Los talleres son una experiencia unica. Aprenda más de lo que esperaba."</p>
                                <cite>- Miembro de los Increíbles -</cite>
                            </div>
                            <div class="flip-card-back">
                                <img src="../img/new_Helen.jpg" alt="Clientes satisfechos">
                                <cite>Equipo Increíbles</cite>
                            </div>
                        </div>
                    </div>
                    <div class="flip-card">
                        <div class="flip-card-inner">
                            <div class="flip-card-front">
                                <div class="quote-icon">
                                    <img src="../img/muscle.png" alt="Icono de comilla">
                                </div>
                                <p>"Los talleres son una experiencia inolvidable; nunca me haban dejado correr tanto."</p>
                                <cite>- Miembro de los Increbles -</cite>
                            </div>
                            <div class="flip-card-back">
                                <img src="../img/new_Jack.jpg" alt="Clientes satisfechos">
                                <cite>Equipo Increíbles</cite>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-grid-3">
                <div class="footer-column">
                    <p>Laboratorio de Edna</p>
                    <p>Mansion de los Increibles<br>Ciudad-Super<br> Calle (ubicacion secreta) </p>
                    <p>edna@ednamoda.com</p>
                </div>

                <div class="footer-column">
                    <p>Politica de privacidad <br>
                        Bases Legales <br>
                        Politica de cookies <br>
                        Aviso legal <br>
                        Politica de compliance <br>
                        Codigo Etico <br></p>
                </div>

                <div class="footer-column">
                    <div class="redes-sociales">
                        <a href="https://facebook.com" target="_blank" aria-label="Facebook"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M16 8h-2a2 2 0 0 0-2 2v8"></path>
                                <line x1="10" y1="13" x2="16" y2="13"></line>
                            </svg></a>
                        <a href="https://instagram.com" target="_blank" aria-label="Instagram"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                            </svg></a>
                        <a href="https://youtube.com" target="_blank" aria-label="YouTube"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path>
                                <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon>
                            </svg></a>
                        <a href="https://linkedin.com" target="_blank" aria-label="LinkedIn"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                                <rect x="2" y="9" width="4" height="12"></rect>
                                <circle cx="4" cy="4" r="2"></circle>
                            </svg></a>
                        <a href="https://twitter.com" target="_blank" aria-label="Twitter/X"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4l11.733 16h4.267l-11.733 -16z"></path>
                                <path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"></path>
                            </svg></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>UNIVERSAL &copy; 2026. Todos los derechos reservados</p>
        </div>
    </footer>
</body>

</html>