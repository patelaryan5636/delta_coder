<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PackPal - Group Logistics Organizer</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: '#96b4b4', secondary: '#c4dfdf' }, borderRadius: { 'none': '0px', 'sm': '4px', DEFAULT: '8px', 'md': '12px', 'lg': '16px', 'xl': '20px', '2xl': '24px', '3xl': '32px', 'full': '9999px', 'button': '8px' } } } }</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .animate-fade-in {
            opacity: 0;
            transform: translateY(10px);
            animation: fadeIn 0.5s ease-out forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animation-delay-100 {
            animation-delay: 0.1s;
        }

        .animation-delay-300 {
            animation-delay: 0.3s;
        }

        .animation-delay-500 {
            animation-delay: 0.5s;
        }

        @keyframes slide-down {
            0% {
                transform: translateY(-100%);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slide-up {
            0% {
                transform: translateY(0);
                opacity: 1;
            }

            100% {
                transform: translateY(-100%);
                opacity: 0;
            }
        }

        .animate-slide-down {
            animation: slide-down 0.3s ease-out;
        }

        .animate-slide-up {
            animation: slide-up 0.3s ease-in;
        }

        :where([class^="ri-"])::before {
            content: "\f3c2";
        }

        .nav-container {
            top: 0;
            left: 0;
            width: 100%;
            z-index: 100;
        }

        .logo {
            font-family: 'Pacifico', serif;
            color: #88ABA5;
            font-size: 1.8rem;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .nav-button {
            transition: all 0.2s ease;
        }

        .nav-button:hover {
            filter: brightness(0.95);
        }

        .profile-container {
            position: relative;
        }

        .profile-container:hover .logout-button {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        @media (max-width: 640px) {
            .nav-content {
                padding: 0.75rem 1rem;
            }

            .logo {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-white font-display">
    <!-- Navbar -->
    <?php
            include("navbar.php");
    ?>

    <!-- Hero Section -->
    <section class="bg-gradient-to-b from-packpal-light-gray to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold text-packpal-navy mb-6 animate-fade-in animation-delay-100">
                    About PackPal: Your Partner in Group Organization
                </h1>
                <p class="text-lg text-gray-600 mb-8 animate-fade-in animation-delay-300">
                    We're on a mission to simplify group packing and logistics for events, travel, and gatherings of all
                    sizes.
                    No more forgotten items, mismatched plans, or coordination headaches.
                </p>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-3xl font-bold text-packpal-navy mb-6">Our Mission</h2>
                <p class="text-gray-600 mb-6">
                    PackPal was born from a simple frustration: the chaos of coordinating group trips. Whether it was
                    family vacations,
                    outdoor adventures with friends, or company retreats, the same problems kept appearing—forgotten
                    essentials, duplicate items,
                    and the endless "who's bringing what" group chats.
                </p>
                <p class="text-gray-600">
                    We set out to create a solution that makes group logistics effortless. Our platform simplifies
                    packing lists,
                    assigns responsibilities, and keeps everyone on the same page—turning what was once a headache into
                    a seamless experience.
                    Today, thousands of groups use PackPal to plan smarter and travel lighter.
                </p>
            </div>
        </div>
    </section>

    <!-- Core Values Section -->
    <section class="bg-packpal-light-gray">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold text-packpal-navy mb-6">Our Core Values</h2>
                <p class="text-gray-600">
                    These principles guide everything we do, from product development to customer support.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div
                    class="bg-white rounded-2xl p-6 shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100">
                    <div class="h-12 w-12 rounded-full bg-packpal-teal/10 flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="text-packpal-teal">
                            <path
                                d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z">
                            </path>
                            <path d="M15.5 11.5h-7"></path>
                            <path d="M12 15V8"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-packpal-navy mb-4">Collaboration</h3>
                    <p class="text-gray-600">
                        We believe that the best outcomes come from working together. Our platform is designed to
                        facilitate seamless
                        collaboration among group members, making coordination effortless and inclusive.
                    </p>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100">
                    <div class="h-12 w-12 rounded-full bg-packpal-teal/10 flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="text-packpal-teal">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="m9 12 2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-packpal-navy mb-4">Simplicity</h3>
                    <p class="text-gray-600">
                        Complexity is the enemy of efficiency. We're committed to creating intuitive, straightforward
                        solutions
                        that eliminate confusion and make group organization accessible to everyone.
                    </p>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100">
                    <div class="h-12 w-12 rounded-full bg-packpal-teal/10 flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="text-packpal-teal">
                            <path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-packpal-navy mb-4">Efficiency</h3>
                    <p class="text-gray-600">
                        Time is precious, especially when planning events and trips. Our tools are designed to save you
                        time,
                        reduce redundancy, and streamline logistics so you can focus on what matters most—enjoying your
                        experience.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold text-packpal-navy mb-6">Meet Our Team</h2>
                <p class="text-gray-600">
                    The passionate people behind PackPal who are dedicated to making group organization effortless.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div
                        class="aspect-square rounded-full overflow-hidden mb-4 mx-auto max-w-[200px] border-4 border-packpal-teal/20">
                        <img src="./assets/img/PYS.jpg"
                            alt="Sarah Johnson" class="w-full h-full object-cover" />
                    </div>
                    <h3 class="text-xl font-semibold text-packpal-navy">Priyanshu Pithadiya</h3>
                    <p class="text-packpal-teal mb-2">Frontend Developer</p>
                </div>

                <div class="text-center">
                    <div
                        class="aspect-square rounded-full overflow-hidden mb-4 mx-auto max-w-[200px] border-4 border-packpal-teal/20">
                        <img src="./assets/img/Rangu.jpg"
                            alt="Michael Chen" class="w-full h-full object-cover" />
                    </div>
                    <h3 class="text-xl font-semibold text-packpal-navy">Rangat Prajapati</h3>
                    <p class="text-packpal-teal mb-2">Backend Developer</p>
                </div>

                <div class="text-center">
                    <div
                        class="aspect-square rounded-full overflow-hidden mb-4 mx-auto max-w-[200px] border-4 border-packpal-teal/20">
                        <img src="./assets/img/Aryan.jpg"
                            alt="Olivia Martinez" class="w-full h-full object-cover" />
                    </div>
                    <h3 class="text-xl font-semibold text-packpal-navy">Aryan Patel</h3>
                    <p class="text-packpal-teal mb-2">Backend Developer</p>
                </div>

                <div class="text-center">
                    <div
                        class="aspect-square rounded-full overflow-hidden mb-4 mx-auto max-w-[200px] border-4 border-packpal-teal/20">
                        <img src="./assets/img/PVN.jpg"
                            alt="James Wilson" class="w-full h-full object-cover" />
                    </div>
                    <h3 class="text-xl font-semibold text-packpal-navy">Pavan Prajapati</h3>
                    <p class="text-packpal-teal mb-2">ML Developer</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-packpal-light-gray">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="font-bold text-2xl mb-4">
                        <span class="text-packpal-teal">Pack</span><span class="text-packpal-navy">Pal</span>
                    </div>
                    <p class="text-gray-600 mb-4">
                        Simplifying group logistics since 2022. Our mission is to make packing and event planning
                        stress-free.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-packpal-gray hover:text-packpal-teal transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                            </svg>
                        </a>
                        <a href="#" class="text-packpal-gray hover:text-packpal-teal transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path
                                    d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z">
                                </path>
                            </svg>
                        </a>
                        <a href="#" class="text-packpal-gray hover:text-packpal-teal transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                            </svg>
                        </a>
                        <a href="#" class="text-packpal-gray hover:text-packpal-teal transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path
                                    d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z">
                                </path>
                                <rect x="2" y="9" width="4" height="12"></rect>
                                <circle cx="4" cy="4" r="2"></circle>
                            </svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-packpal-navy mb-4">Product</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-600 hover:text-packpal-teal transition-colors">Features</a>
                        </li>
                        <li><a href="#" class="text-gray-600 hover:text-packpal-teal transition-colors">Pricing</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-packpal-teal transition-colors">Templates</a>
                        </li>
                        <li><a href="#" class="text-gray-600 hover:text-packpal-teal transition-colors">Mobile App</a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-packpal-navy mb-4">Resources</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-600 hover:text-packpal-teal transition-colors">Help Center</a>
                        </li>
                        <li><a href="#" class="text-gray-600 hover:text-packpal-teal transition-colors">Blog</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-packpal-teal transition-colors">Guides</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-packpal-teal transition-colors">Events</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-packpal-navy mb-4">Company</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-600 hover:text-packpal-teal transition-colors">About Us</a>
                        </li>
                        <li><a href="#" class="text-gray-600 hover:text-packpal-teal transition-colors">Careers</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-packpal-teal transition-colors">Press</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-packpal-teal transition-colors">Contact</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-200 mt-12 pt-8 text-center">
                <p class="text-gray-600 text-sm">© 2023 PackPal. All rights reserved.</p>
                <div class="flex justify-center space-x-6 mt-4">
                    <a href="#" class="text-gray-600 hover:text-packpal-teal transition-colors text-sm">Terms of
                        Service</a>
                    <a href="#" class="text-gray-600 hover:text-packpal-teal transition-colors text-sm">Privacy
                        Policy</a>
                    <a href="#" class="text-gray-600 hover:text-packpal-teal transition-colors text-sm">Cookie
                        Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.getElementById('menuToggle');
            const mobileMenu = document.getElementById('mobileMenu');
            const menuIcon = document.getElementById('menuIcon');
            const closeIcon = document.getElementById('closeIcon');

            menuToggle.addEventListener('click', function () {
                if (mobileMenu.classList.contains('hidden')) {
                    // Slide down animation
                    mobileMenu.classList.remove('hidden', 'animate-slide-up');
                    mobileMenu.classList.add('animate-slide-down', 'block');
                } else {
                    // Slide up animation
                    mobileMenu.classList.remove('animate-slide-down');
                    mobileMenu.classList.add('animate-slide-up');

                    // Delay hiding the menu until the animation ends
                    mobileMenu.addEventListener(
                        'animationend',
                        function () {
                            mobileMenu.classList.remove('block');
                            mobileMenu.classList.add('hidden');
                        },
                        { once: true } // Ensures this event listener runs only once per toggle
                    );
                }

                // Toggle menu and close icons
                menuIcon.classList.toggle('hidden');
                closeIcon.classList.toggle('hidden');
            });
        });
    </script>
</body>

</html>