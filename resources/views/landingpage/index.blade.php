<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Perusahaan Logistik yang Aman dan Tepercaya. Memberikan Layanan Terbaik pada Pelanggan dan telah teruji selama lebih dari 18 tahun">
    <meta name="keywords" content="Logistic, Exkspedisi, Expedition, Kapal Laut">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PT RAS | Rahmat Alam Samudera </title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Lora:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">

    <link rel="stylesheet" href="{{ url('') }}/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="{{ url('') }}/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="{{ url('') }}/css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="{{ url('') }}/css/flaticon.css" type="text/css">
    <link rel="stylesheet" href="{{ url('') }}/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="{{ url('') }}/css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="{{ url('') }}/css/jquery-ui.min.css" type="text/css">
    <link rel="stylesheet" href="{{ url('') }}/css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="{{ url('') }}/css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="{{ url('') }}/css/style.css" type="text/css">
    <style>
        /* Atur hero-section */
        .hero-section {
            position: relative;
            overflow: hidden;
            height: 100vh;
            /* Sesuaikan tinggi section sesuai kebutuhan */
            margin: 0;
            /* Hapus margin */
            padding: 0;
            /* Hapus padding */
        }

        /* Video container di hero-section */
        .video-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            /* Pastikan video berada di belakang teks */
            overflow: hidden;
        }

        /* Video element di hero-section */
        #header-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Mengisi area kontainer tanpa merusak aspek rasio */
        }

        /* Teks di hero-section */
        .hero-text {
            position: relative;
            z-index: 1;
            /* Teks berada di atas video */
            color: #fff;
            /* Warna teks */
            padding: 10px;
            max-width: 80%;
            margin: auto;
            min-width: 500px;
            margin-top: 50px;
        }


        @media (max-width: 768px) {
            .hero-section {
                height: 60vh;
                /* Kurangi tinggi untuk perangkat yang lebih kecil */
            }

            .hero-text {
                font-size: 14px;
                /* Ukuran font */
                padding: 10px;
                /* Padding */

            }

            .hero-text h1 {
                font-size: 2rem;
                max-width: 80%;
                min-width: 100px;
            }

            .hero-text p {
                font-size: 1rem;
                max-width: 70%;
                min-width: 100px;
            }

            .logo {
                flex-direction: row;
                align-items: center;
            }

            .logo a {
                margin-left: 10px;
                min-width: 4px;
            }

            .logo i {
                margin-left: 8px;

            }
        }




        .services-section {
            position: relative;
            overflow: hidden;
            padding: 60px 0;
            color: #fff;
            margin-top: 30px;
            /* Hilangkan jarak antara section */
            margin: 0;
            padding: 0;
        }

        .video-container-services {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .video-container-services video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>

</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <!-- Offcanvas Menu Section Begin -->
    <div class="offcanvas-menu-overlay"></div>
    <div class="canvas-open">
        <i class="icon_menu"></i>
    </div>
    <div class="offcanvas-menu-wrapper">
        <div class="canvas-close">
            <i class="icon_close"></i>
        </div>
        <nav class="mainmenu mobile-menu">
            <ul>
                <li><a href="#service">Services</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#mission">Mission</a></li>
                <li><a href="#gallery">Gallery</a></li>
                <li class="active"><a href="{{ route('login') }}">Login</a></li>

            </ul>
        </nav>
        <div id="mobile-menu-wrap"></div>


    </div>
    <!-- Offcanvas Menu Section End -->

    <!-- Header Section Begin -->
    <header class="header-section">

        <div class="menu-item">
            <div class="container">
                <div class="row">
                    <div class="col-lg-2">
                        <div class="logo" style="display: flex; align-items: center;">
                            <i class="fa-solid fa-mobile-screen fa-2x" style="color: #343a40;"></i>
                            <a href="" style="color: #343a40; margin-left: 16px; font-size: 14px; min-width: 16vh;">Telp
                                <br> (031-7495507)</a>
                            <i class="fa-sharp fa-solid fa-house-building fa-2x"
                                style="color: #6060b6; margin-left: 10px;"></i>
                            <img height="36" src="https://img.icons8.com/ios-filled/50/000000/company.png" alt="company"
                                style="margin-left: 10px;">
                            <a href=""
                                style="color: #343a40; margin-left: 16px; font-size: 14px; min-width: 1000px;">Jl.
                                Kalianak Blog G,<br>No. 55 Surabaya</a>

                        </div>

                    </div>
                    <div class="col-lg-10">
                        <div class="nav-menu " style="margin-top: 1vh;">
                            <nav class="mainmenu mr-10">
                                <ul>
                                    <li><a href="#service">Services</a></li>
                                    <li><a href="#about">About Us</a></li>
                                    <li><a href="#mission">Mission</a></li>
                                    <li><a href="#gallery">Gallery</a></li>
                                    <li class="active"><a href="{{ route('login') }}">Login</a></li>

                                </ul>
                            </nav>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Header End -->

    <!-- Hero Section Begin -->
    <section id="home" class="services-section spad" placeholder="home">
        <div class="video-container">
            <video autoplay muted loop poster="hero1.jpg" id="header-video">
                <source src="img/laut.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>

        <div class="container" style="padding-bottom: 80px;">
            <div class="row">
                <div class="col-lg-7">
                    <div class="hero-text">
                        <h1 style="line-height: normal;">RAHMAT ALAM SAMUDERA</h1>
                        <p style="max-width: 300px;"> Perusahaan Logistik yang Aman dan Tepercaya. Memberikan Layanan
                            Terbaik pada Pelanggan
                            dan telah teruji selama lebih dari 18 tahun.</p>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <!-- Hero Section End -->
    <!-- Services Section Begin -->
    <section id="service" class="services-section spad" placeholder="service" style="align-items: center;">
        <div class="video-container">
            <video autoplay muted loop poster="hero1.jpg" id="header-video">
                <source src="img/down.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">

                        <h2 style="color: #fff; padding-top: 80px;">Our Services</h2>
                    </div>
                </div>
            </div>
            <div class="row" style="padding-bottom: 90px;">
                <div class="col-lg-4 col-sm-6">
                    <div class="service-item">
                        <i class="flaticon-036-parking"></i>
                        <h4>Freight Forwarding</h4>
                        <p>Melayani dan mengatur pengiriman barang anda ke seluruh Indonesia. Didukung project cargo
                            Full Container Load
                            (FCL) dan Les Container Load (LCL) serta sumber daya manusia yang berkualitas.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="service-item">
                        <i class="flaticon-033-dinner"></i>
                        <h4>Divisi Trucking</h4>
                        <p>Pilihan anda untuk mengirimkan barang. Didukung mitra kerja untuk penambahan jumlah unit
                            armada yang bervariasi sesuai kebutuhan.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="service-item">
                        <i class="flaticon-026-bed"></i>
                        <h4>Divisi Warehouse</h4>
                        <p>
                            Luas gudang 3200m² dengan daya tampung maksimum sebesar 1000m², dilengkapi dengan fasilitas
                            forklift dan tenaga bongkar muat internal.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Services Section End -->
    <!-- About Us Section Begin -->
    <section id="about" class="about-section spad" placeholder="about">
        <div class="container">
            <div class="row" style="padding-top: 60px;">
                <div class="col-lg-5 text-center">
                </div>
                <div class="col-lg-7">
                    <div class="section-title">
                        <h2>Rahmat Alam Samudera</h2>
                        <br>
                    </div>
                </div>
                <div class="col-lg-5 text-center">
                    <img src="img/ras.png" alt="Logo Rahmat Alam Samudera" class="about-logo">
                </div>
                <div class="col-lg-7">

                    <div class="about-text">
                        <p>PT. Rahmat Alam Samudera yang telah berdiri sejak tahun 2007 adalah perusahaan yang bergerak
                            di bidang Logistik meliputi: Freight Forwarding, Inland Service, Project Cargo, dan
                            Warehousing yang didukung dengan segenap sumber daya berkualitas dan berintegritas yang
                            tersebar di seluruh wilayah Indonesia, sehingga menempatkan PT. Rahmat Alam Samudera sebagai
                            salah satu perusahaan penyedia layanan logistik yang lengkap, padu, dan handal.</p>
                    </div>
                </div>

            </div>
        </div>
        </div>
    </section>
    <!-- About Us Section End -->

    <!-- Testimonial Section Begin -->

    <!-- BAGIAN SERVICE -->
    <div class="row">
        <div class="col-lg-12">
            <div class="section-title">
                <h2 style="color: #ff3d51; padding-top: 90px;">Menyambungkan Seluruh Indonesia</h2>
            </div>
        </div>
    </div>
    <section id="mission" class="services-section spad" placeholder="mission">

        <div class="video-container">
            <img src="img/peta.png" alt="Misi Kami" id="header-image" style="width: 100%; height: auto;">
        </div>
        <div class="container">
            <div class="row" style="padding-bottom: 90px; padding-top: 90px;">
                <div class="col-lg-6 col-sm-6">
                    <div class="service-items">
                        <h2 style="color: #000000; text-align: start; font-weight: bold;">
                            <span class="highlight-background"
                                style="background-color: rgba(236, 233, 233, 0.8); padding: 5px 10px; border-radius: 5px;">VISI</span>
                        </h2><br>
                        <p style="text-align: start;">Menjadi Perusahaan Logistik Terbaik dan Tepercaya, yang Mampu
                            Menggerakkan Perekonomian dengan Layanan Berkualitas Tinggi dan Inovasi Berkelanjutan.</p>
                        <br><br>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6">
                    <div class="service-items">
                        <h2 style="color: #000000; text-align: end; font-weight: bold;">
                            <span class="highlight-background"
                                style="background-color: rgba(233, 234, 236, 0.8);padding: 5px 10px; border-radius: 5px;">MISI</span>
                        </h2><br>
                        <p style="text-align: end;">
                            Memberikan Layanan Terbaik dan Tepercaya, Didukung Sarana Prasarana yang Memadai, Jaringan
                            Terluas dan Sumber Daya yang Berkualitas dan Berintegritas.
                        </p><br><br>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonial Section End -->

    <!-- Home Room Section Begin -->

    <!-- Home Room Section End -->



    <!-- Blog Section Begin -->
    <section id="gallery" class="blog-section spad" placeholder="gallery">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <h2>Gallery</h2><br><br>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="blog-item set-bg" data-setbg="img/blog/t3.jpg">
                        <div class="bi-text">
                            <span class="b-tag">Trucking</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="blog-item set-bg" data-setbg="img/blog/wh2.jpg">
                        <div class="bi-text">
                            <span class="b-tag">Warehouse</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="blog-item set-bg" data-setbg="img/blog/t1.jpg">
                        <div class="bi-text">
                            <span class="b-tag">Trucking</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="blog-item small-size set-bg" data-setbg="img/blog/wh4.jpg">
                        <div class="bi-text">
                            <span class="b-tag">Warehouse</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="blog-item small-size set-bg" data-setbg="img/blog/ft9.jpg">
                        <div class="bi-text">
                            <span class="b-tag">Warehouse</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
    <!-- Blog Section End -->

    <!-- Footer Section Begin -->
    <footer class="footer-section">
        <div class="container">
            <div class="footer-text">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="ft-about">
                            <h6 style="font-size: 14px;
                           color: #ff3d51;
                           text-transform: uppercase;
                           font-weight: 700;
                           letter-spacing: 3px;
                           font-family: Cabin, sans-serif;
                           margin-bottom: 20px;">Profil</h6>
                            <img src="img/ras.png" style="max-width: 120px; max-height: 80px;" alt=""><br>
                            <p><br>PT Rahmat Alam Samudera Melayani Pelanggan Kami dengan Solusi Rantai Pasok
                                Terintegrasi yang Mengirimkan Barang Melalui Laut dan Udara .</p>
                                <div class="ft-about">
                                    <div class="fa-social">
                                        <a href="https://wa.me/6281230162999"><i class="fa fa-whatsapp"></i></a>
                                        <a href="mailto:dwi@ptras.id"><i class="fa fa-envelope"></i></a>
                                        <a href="https://maps.app.goo.gl/TYVPApe1GyqD1ML59"><i class="fa fa-map"></i></a>
                                    </div>
                                </div>
                        </div>
                    </div>
                    <div class="col-lg-3 offset-lg-1">
                        <div class="ft-contact">
                            <h6>Kontak Kami</h6>
                            <ul>
                                <li>Telp. Number : 08xx-xxxx-xxxx</li>
                                <li>Whatsapp : 08xx-xxxx-xxxx</li>
                                <li>Email : info@ptras.id</li>
                                <li></li>

                            </ul><br><br>
                            <h6>Kantor Pusat</h6>
                            <ul>
                                <li>Jl. Kalianak Blog G, No. 55 Surabaya</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 offset-lg-1">
                        <div class="ft-contact">
                            <div class="ft-newslatter">
                                <h6>Kirim pesan</h6>
                                <p>Masukkan Email dan Pesan Anda dan Kami Akan Menghubungi Anda </p>
                                <form id="emailForm" method="POST" class="fn-form">
                                    <input type="text" name="name" placeholder="Nama*" required>
                                    <input type="email" name="email" placeholder="Email*"
                                        style="margin-top: 2px;" required>
                                    <input name="message" placeholder="Pesan*" style="margin-top: 2px;"
                                        required>
                                    <button type="submit" style="margin-top: 5px;">Kirim Pesan <i class="fa fa-send"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright-option">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <ul>
                            <li><a href="#home">Home</a></li>
                            <li><a href="#service">Services</a></li>
                            <li><a href="#about">About Us</a></li>
                            <li><a href="#mission">Mission</a></li>
                            <li><a href="#gallery">Gallery</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-5">
                        <div class="co-text">
                            <p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                                Copyright &copy;
                                <script>document.write(new Date().getFullYear());</script> All rights reserved | Rahmat
                                Alam Samudera
                                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer Section End -->

    <!-- Search model Begin -->
    <div class="search-model">
        <div class="h-100 d-flex align-items-center justify-content-center">
            <div class="search-close-switch"><i class="icon_close"></i></div>
            <form class="search-model-form">
                <input type="text" id="search-input" placeholder="Search here.....">
            </form>
        </div>
    </div>
    <!-- Search model end -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Smooth Scroll Script -->
    <script>
        $(document).ready(function () {
            // Add smooth scrolling to all links
            $("a").on('click', function (event) {

                // Make sure this.hash has a value before overriding default behavior
                if (this.hash !== "") {
                    // Prevent default anchor click behavior
                    event.preventDefault();

                    // Store hash
                    var hash = this.hash;

                    // Using jQuery's animate() method to add smooth page scroll
                    // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
                    $('html, body').animate({
                        scrollTop: $(hash).offset().top
                    }, 800, function () {

                        // Add hash (#) to URL when done scrolling (default click behavior)
                        window.location.hash = hash;
                    });
                } // End if
            });
        });
    </script>

    <!-- Js Plugins -->
    <script src="{{ url('') }}/js/jquery-3.3.1.min.js"></script>
    <script src="{{ url('') }}/js/bootstrap.min.js"></script>
    <script src="{{ url('') }}/js/jquery.magnific-popup.min.js"></script>
    <script src="{{ url('') }}/js/jquery.nice-select.min.js"></script>
    <script src="{{ url('') }}/js/jquery-ui.min.js"></script>
    <script src="{{ url('') }}/js/jquery.slicknav.js"></script>
    <script src="{{ url('') }}/js/owl.carousel.min.js"></script>
    <script src="{{ url('') }}/js/main.js"></script>
    <!-- <script type="text/javascript" src="https://cdn.emailjs.com/dist/email.min.js"></script>
    <script type="text/javascript">
        (function () {
            emailjs.init("pZaeyMIVJRBkQX-I1"); // Ganti dengan User ID EmailJS Anda
        })();
    </script>
    <script>
        document.getElementById('emailForm').addEventListener('submit', function (event) {
            event.preventDefault(); // Menghentikan pengiriman formulir default

            // Ambil nilai input dari formulir
            var formData = new FormData(document.getElementById('emailForm'));
            var name = formData.get('name');
            var email = formData.get('email');
            var message = formData.get('message');

            // Kirim email menggunakan EmailJS
            emailjs.send("service_mis13s7", "template_6c54xze", {
                name: name,
                email: email,
                message: message
            })
                .then(function (response) {
                    console.log('Email berhasil terkirim!', response);
                    alert('Email berhasil terkirim!');
                    // Redirect ke halaman atau lakukan tindakan lain setelah berhasil
                    window.location.href = '/'; // Contoh pengalihan ke halaman home
                }, function (error) {
                    console.error('Gagal mengirim email:', error);
                    alert('Gagal mengirim email. Silakan coba lagi.');
                });
        });
    </script> -->
</body>

</html>
