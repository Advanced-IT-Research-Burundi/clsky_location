<!-- Début du pied de page -->
<div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-3 col-md-6">
                <h5 class="text-white mb-4">Contactez-nous</h5>
                <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Burundi, Bujumbura</p>
                <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+257 69 34 53 45</p>
                <p class="mb-2"><i class="fa fa-envelope me-3"></i>clskyappartement@gmail.com</p>
                {{-- <div class="d-flex pt-2">
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-youtube"></i></a>
                    <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-linkedin-in"></i></a>
                </div> --}}
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="text-white mb-4">Liens Rapides</h5>
                <a class="btn btn-link text-white-50" href="{{ route('about') }}">À Propos de Nous</a>
                <a class="btn btn-link text-white-50" href="{{ route('contact') }}">Contactez-nous</a>
                {{-- <a class="btn btn-link text-white-50" href="{{ route('services') }}">Nos Services</a> --}}
                {{-- <a class="btn btn-link text-white-50" href="">Politique de Confidentialité</a>
                <a class="btn btn-link text-white-50" href="">Termes et Conditions</a> --}}
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="text-white mb-4">Galerie Photo</h5>
                <div class="row g-2 pt-2">
                    <div class="col-4">
                        <img class="img-fluid rounded bg-light p-1" src="{{ asset('img/property-1.jpg') }}"
                            alt="">
                    </div>
                    <div class="col-4">
                        <img class="img-fluid rounded bg-light p-1" src="{{ asset('img/property-2.jpg') }}"
                            alt="">
                    </div>
                    <div class="col-4">
                        <img class="img-fluid rounded bg-light p-1" src="{{ asset('img/property-3.jpg') }}"
                            alt="">
                    </div>
                    <div class="col-4">
                        <img class="img-fluid rounded bg-light p-1" src="{{ asset('img/property-4.jpg') }}"
                            alt="">
                    </div>
                    <div class="col-4">
                        <img class="img-fluid rounded bg-light p-1" src="{{ asset('img/property-5.jpg') }}"
                            alt="">
                    </div>
                    <div class="col-4">
                        <img class="img-fluid rounded bg-light p-1" src="{{ asset('img/property-6.jpg') }}"
                            alt="">
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="text-white mb-4">Cl Sky appartement</h5>
                <p>Rejoignez la communauté CL Sky Appartement et recevez en avant-première 
                    les nouveaux appartements disponibles, les promotions spéciales et les mises 
                    à jour importantes.</p>
            </div>

        </div>
    </div>
    <div class="container">
        <div class="copyright">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    &copy; <a class="border-bottom" href="#">CL SKY COMPANY</a>, Tous Droits Réservés.
                    Conçu par
                    <a class="border-bottom" href="http://advanceditb.com">
                        Advanced IT & Research Burundi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fin du pied de page -->
