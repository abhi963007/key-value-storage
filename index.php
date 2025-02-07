<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CloudStore - Next-Gen File Storage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Architects+Daughter&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tsparticles@2.12.0/tsparticles.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Architects Daughter', 'Inter', sans-serif;
            background-color: #0f172a;
        }
        .architects-font {
            font-family: 'Architects Daughter', cursive;
        }
        .gradient-text {
            background: linear-gradient(135deg, #818cf8 0%, #a78bfa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-gradient {
            background: radial-gradient(circle at top right, rgba(129, 140, 248, 0.1) 0%, transparent 60%),
                        radial-gradient(circle at bottom left, rgba(167, 139, 250, 0.1) 0%, transparent 60%);
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
        }
        #tsparticles {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            pointer-events: none;
        }
        .content-wrapper {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body class="bg-slate-900 text-white">
    <!-- Particles Container -->
    <div id="tsparticles"></div>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="hero-gradient min-h-screen">
            <!-- Header -->
            <header class="fixed w-full top-0 z-50 bg-slate-900/80 backdrop-blur-md border-b border-slate-800">
                <div class="container mx-auto px-4 py-4">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span class="text-xl font-bold gradient-text architects-font">CloudStore</span>
                        </div>
                        <div>
                            <a href="dashboard/" class="px-4 py-2 rounded-full bg-indigo-600 text-white hover:bg-indigo-700 transition-colors architects-font">
                                Launch App
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Hero Section -->
            <section class="pt-32 pb-20">
                <div class="container mx-auto px-4">
                    <div class="flex flex-col lg:flex-row items-center justify-between gap-16">
                        <div class="lg:w-1/2 space-y-8">
                            <h1 class="text-5xl lg:text-7xl font-bold architects-font">
                                <span class="text-white">Store Files</span>
                                <br/>
                                <span class="gradient-text">Seamlessly</span>
                            </h1>
                            <p class="text-xl text-slate-300 architects-font">
                                Experience the future of file storage with our cutting-edge platform. 
                                Upload, manage, and share your files with unparalleled ease.
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <a href="dashboard/" class="inline-flex items-center justify-center px-8 py-4 rounded-full bg-indigo-600 text-white hover:bg-indigo-700 transition-colors text-lg font-medium architects-font">
                                    Get Started
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </a>
                                <a href="#features" class="inline-flex items-center justify-center px-8 py-4 rounded-full border-2 border-slate-700 hover:border-indigo-500 text-slate-300 hover:text-indigo-400 transition-colors text-lg font-medium architects-font">
                                    Learn More
                                </a>
                            </div>
                        </div>
                        <div class="lg:w-1/2">
                            <div class="relative">
                                <div class="absolute inset-0 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full filter blur-3xl opacity-20 animate-pulse"></div>
                                <lottie-player
                                    src="https://assets3.lottiefiles.com/packages/lf20_KvK0ZJBQzu.json"
                                    background="transparent"
                                    speed="1"
                                    class="w-full max-w-2xl mx-auto animate-float"
                                    loop
                                    autoplay
                                ></lottie-player>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section id="features" class="py-20 bg-slate-800/50 backdrop-blur-lg">
                <div class="container mx-auto px-4">
                    <h2 class="text-3xl lg:text-4xl font-bold text-center mb-12">
                        <span class="gradient-text architects-font">Powerful Features</span>
                    </h2>
                    <div class="grid md:grid-cols-3 gap-8">
                        <!-- Feature 1 -->
                        <div class="glass-card p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                            <div class="w-14 h-14 bg-indigo-500/20 rounded-xl flex items-center justify-center mb-6">
                                <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-4 architects-font">Drag & Drop Upload</h3>
                            <p class="text-slate-300 architects-font">Simply drag your files into the upload area. No complicated steps required.</p>
                        </div>

                        <!-- Feature 2 -->
                        <div class="glass-card p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                            <div class="w-14 h-14 bg-purple-500/20 rounded-xl flex items-center justify-center mb-6">
                                <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-4 architects-font">Secure Storage</h3>
                            <p class="text-slate-300 architects-font">Your files are encrypted and stored with enterprise-grade security.</p>
                        </div>

                        <!-- Feature 3 -->
                        <div class="glass-card p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                            <div class="w-14 h-14 bg-pink-500/20 rounded-xl flex items-center justify-center mb-6">
                                <svg class="w-8 h-8 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-4 architects-font">Easy Sharing</h3>
                            <p class="text-slate-300 architects-font">Share your files instantly with secure, customizable links.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Stats Section -->
            <section class="py-20">
                <div class="container mx-auto px-4">
                    <div class="grid md:grid-cols-3 gap-8 text-center">
                        <div class="glass-card p-8 rounded-2xl">
                            <div class="text-4xl font-bold gradient-text mb-2 architects-font">100%</div>
                            <div class="text-slate-300 architects-font">Secure Storage</div>
                        </div>
                        <div class="glass-card p-8 rounded-2xl">
                            <div class="text-4xl font-bold gradient-text mb-2 architects-font">24/7</div>
                            <div class="text-slate-300 architects-font">File Access</div>
                        </div>
                        <div class="glass-card p-8 rounded-2xl">
                            <div class="text-4xl font-bold gradient-text mb-2 architects-font">Fast</div>
                            <div class="text-slate-300 architects-font">Upload Speed</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="py-8 border-t border-slate-800">
                <div class="container mx-auto px-4 text-center text-slate-400">
                    <p class="architects-font">© 2025 CloudStore. All rights reserved.</p>
                </div>
            </footer>
        </div>
    </div>

    <script>
        // Particle configuration
        tsParticles.load("tsparticles", {
            fpsLimit: 60,
            particles: {
                number: {
                    value: 80,
                    density: {
                        enable: true,
                        value_area: 1000
                    }
                },
                color: {
                    value: ["#818cf8", "#a78bfa", "#6366f1"]
                },
                shape: {
                    type: ["circle", "triangle"],
                    stroke: {
                        width: 1,
                        color: "#6366f1"
                    }
                },
                opacity: {
                    value: 0.6,
                    random: {
                        enable: true,
                        minimumValue: 0.2
                    },
                    animation: {
                        enable: true,
                        speed: 0.5,
                        minimumValue: 0.1,
                        sync: false
                    }
                },
                size: {
                    value: 4,
                    random: {
                        enable: true,
                        minimumValue: 1
                    },
                    animation: {
                        enable: true,
                        speed: 2,
                        minimumValue: 1,
                        sync: false
                    }
                },
                links: {
                    enable: true,
                    distance: 150,
                    color: "#6366f1",
                    opacity: 0.3,
                    width: 1,
                    triangles: {
                        enable: true,
                        opacity: 0.1
                    }
                },
                move: {
                    enable: true,
                    speed: 1,
                    direction: "none",
                    random: true,
                    straight: false,
                    outModes: {
                        default: "bounce"
                    },
                    attract: {
                        enable: true,
                        rotateX: 1200,
                        rotateY: 1200
                    }
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onHover: {
                        enable: true,
                        mode: ["grab", "bubble"]
                    },
                    onClick: {
                        enable: true,
                        mode: "repulse"
                    },
                    resize: true
                },
                modes: {
                    grab: {
                        distance: 180,
                        links: {
                            opacity: 0.8
                        }
                    },
                    bubble: {
                        distance: 200,
                        size: 6,
                        duration: 0.4,
                        opacity: 0.8
                    },
                    repulse: {
                        distance: 200,
                        duration: 0.4
                    }
                }
            },
            background: {
                color: "transparent"
            },
            detectRetina: true,
            themes: [
                {
                    name: "night",
                    default: {
                        value: true,
                        mode: "dark"
                    }
                }
            ]
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html> 